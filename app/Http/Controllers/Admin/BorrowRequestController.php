<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Models\Book;
use App\Models\Notification;
use App\Services\WaitlistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;

class BorrowRequestController extends Controller
{
    public function __construct(private WaitlistService $waitlistService)
    {
    }

    public function index(): View
    {
        $borrows = BorrowRequest::with([
                'book' => fn($q) => $q->withTrashed(),
                'user'
            ])
            ->orderByRaw("FIELD(status, 'pending','approved','borrowed','declined','returned')")
            ->orderByDesc('requested_at')
            ->paginate(20);

        return view('admin.borrows.index', [
            'borrows' => $borrows,
        ]);
    }

    public function approve(Request $request, BorrowRequest $borrow): RedirectResponse
    {
        try {
            DB::transaction(function () use ($borrow) {
                $book = Book::lockForUpdate()->find($borrow->book_id);
                if (!$book) {
                    throw new \RuntimeException('Book not found.');
                }
                if ($book->copies_available <= 0) {
                    throw new \RuntimeException('No available copies to approve this request.');
                }

                // Reserve a copy on approval so admin library reflects pending reservation
                $book->decrement('copies_available');

                $borrow->update([
                    'status' => 'approved',
                    'due_at' => $borrow->due_at ?? Carbon::now()->addDays($this->defaultBorrowDays()),
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $this->notify($borrow, 'borrow_approved', 'Your borrow request was approved.');

        return back()->with('status', 'Borrow request approved.');
    }

    public function decline(Request $request, BorrowRequest $borrow): RedirectResponse
    {
        try {
            DB::transaction(function () use ($borrow) {
                // If this was previously approved (reserved), return the reserved copy
                if ($borrow->status === 'approved') {
                    $book = Book::lockForUpdate()->find($borrow->book_id);
                    if ($book) {
                        $book->increment('copies_available');
                        if ($book->copies_available > $book->copies_total) {
                            $book->update(['copies_available' => $book->copies_total]);
                        }
                    }
                }

                $borrow->update([
                    'status' => 'declined',
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $this->notify($borrow, 'borrow_declined', 'Your borrow request was declined.');

        return back()->with('status', 'Borrow request declined.');
    }

    public function markBorrowed(Request $request, BorrowRequest $borrow): RedirectResponse
    {
        if ($borrow->status !== 'approved') {
            return back()->with('error', 'Request must be approved before marking as borrowed.');
        }

        try {
            DB::transaction(function () use ($borrow) {
                $now = Carbon::now();
                $defaultDays = $this->defaultBorrowDays();
                $dueAt = $borrow->due_at ?? $now->copy()->addDays($defaultDays);

                // Cap the due date to the maximum allowed borrow duration
                $maxDays = $this->maxBorrowDays();
                $cap = $now->copy()->addDays($maxDays);
                if ($dueAt->gt($cap)) {
                    $dueAt = $cap;
                }

                // Do not decrement here — copies were reserved on approval.
                $borrow->update([
                    'status' => 'borrowed',
                    'due_at' => $dueAt,
                    'borrowed_at' => $now,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        // Refresh and notify the user that their borrow is now active
        $borrow->refresh();
        $due = $borrow->due_at ? $borrow->due_at->format('M j, Y') : 'N/A';
        $this->notify($borrow, 'borrow_marked_borrowed', 'Your borrow has been marked as borrowed. Due: ' . $due);

        return back()->with('status', 'Book marked as borrowed.');
    }

    public function markReturned(Request $request, BorrowRequest $borrow): RedirectResponse
    {
        if ($borrow->status !== 'borrowed') {
            return back()->with('error', 'Only borrowed items can be returned.');
        }

        DB::transaction(function () use ($borrow) {
            $book = Book::lockForUpdate()->find($borrow->book_id);
            if ($book) {
                $book->increment('copies_available');
                if ($book->copies_available > $book->copies_total) {
                    $book->update(['copies_available' => $book->copies_total]);
                }
            }

            $borrow->update([
                'status' => 'returned',
                'returned_at' => Carbon::now(),
            ]);
        });

        $this->waitlistService->notifyNextForBook($borrow->book_id);

        // Notify the user that their borrow was returned
        $borrow->refresh();
        $this->notify($borrow, 'borrow_returned', 'Your borrowed item has been marked as returned.');

        return back()->with('status', 'Book marked as returned.');
    }

    /**
     * Allow admin to make a returned book available (e.g. after inspection).
     */
    public function makeAvailable(Request $request, BorrowRequest $borrow): RedirectResponse
    {
        if ($borrow->status !== 'returned') {
            return back()->with('error', 'Only returned items can be made available.');
        }

        DB::transaction(function () use ($borrow) {
            $book = Book::lockForUpdate()->find($borrow->book_id);
            if ($book) {
                // Only increment if there is room (avoid over-incrementing)
                if ($book->copies_available < $book->copies_total) {
                    $book->increment('copies_available');
                }
            }

            // Record admin confirmation timestamp (structured)
            $borrow->update([
                'admin_confirmed_at' => Carbon::now(),
                // keep previous notes intact
                'notes' => trim(($borrow->notes ? $borrow->notes . ' | ' : '') . 'Admin confirmed return on ' . Carbon::now()->toDateTimeString()),
            ]);
        });

        // Notify waitlist if anything changed
        $this->waitlistService->notifyNextForBook($borrow->book_id);

        // Record audit log for admin confirmation
        AuditLog::create([
            'admin_id' => auth()->id(),
            'action' => 'confirmed_return',
            'target_type' => 'borrow_request',
            'target_id' => $borrow->id,
        ]);

        // Notify the user that the admin confirmed the return and made the book available
        $this->notify($borrow, 'borrow_return_confirmed', 'An admin confirmed your return and made the book available.');

        return back()->with('status', 'Book marked available.');
    }

    private function defaultBorrowDays(): int
    {
        $value = DB::table('settings')->where('key', 'default_borrow_duration_days')->value('value');
        return (int) ($value ?? 7);
    }

    private function maxBorrowDays(): int
    {
        $value = DB::table('settings')->where('key', 'max_borrow_duration_days')->value('value');
        return (int) ($value ?? 15);
    }

    private function notify(BorrowRequest $borrow, string $type, string $message): void
    {
        Notification::create([
            'user_id' => $borrow->user_id,
            'type' => $type,
            'message' => $message,
            'target_type' => 'borrow_request',
            'target_id' => $borrow->id,
        ]);
    }
}
