<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BorrowRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BorrowRequestController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
        ]);

        $book = Book::where('is_active', true)->findOrFail($data['book_id']);

        $hasActive = BorrowRequest::query()
            ->where('user_id', $request->user()->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'approved', 'borrowed'])
            ->exists();

        if ($hasActive) {
            return back()->with('error', 'You already have an active request for this book.');
        }

        $borrow = BorrowRequest::create([
            'user_id' => $request->user()->id,
            'book_id' => $book->id,
            'status' => 'pending',
            'requested_at' => Carbon::now(),
        ]);

        // Notify admins about the new borrow request
        \App\Models\AdminNotification::create([
            'admin_id' => $request->user()->id,
            'user_id' => $request->user()->id,
            'title' => 'New borrow request',
            'message' => $request->user()->name . ' requested to borrow "' . ($book->title ?? 'a book') . '"',
            'type' => 'info',
            'is_broadcast' => true,
        ]);

        return back()->with('status', 'Borrow request submitted.');
    }

    /**
     * Allow the student to return a borrowed book before due date.
     */
    public function return(Request $request, BorrowRequest $borrow): RedirectResponse
    {
        if ($borrow->user_id !== $request->user()->id) {
            return back()->with('error', 'Not your borrow.');
        }
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

        return back()->with('status', 'Book returned. Thank you.');
    }

    /**
     * Request extension for a borrowed book. Adds 7 days up to max configured days (default 15).
     */
    public function extend(Request $request, BorrowRequest $borrow): RedirectResponse
    {
        if ($borrow->user_id !== $request->user()->id) {
            return back()->with('error', 'Not your borrow.');
        }
        if ($borrow->status !== 'borrowed') {
            return back()->with('error', 'Only active borrows can be extended.');
        }
        if (!$borrow->due_at) {
            return back()->with('error', 'No due date set.');
        }

        $maxDays = (int) (DB::table('settings')->where('key', 'max_borrow_duration_days')->value('value') ?? 15);
        $from = $borrow->borrowed_at ?? $borrow->due_at->copy()->subDays(7);
        $cap = $from->copy()->addDays($maxDays);
        $newDue = $borrow->due_at->copy()->addDays(7);

        if ($borrow->due_at->gte($cap)) {
            return back()->with('error', 'Maximum borrow period (' . $maxDays . ' days) already reached.');
        }
        if ($newDue->gt($cap)) {
            $newDue = $cap;
        }

        $borrow->update(['due_at' => $newDue]);

        return back()->with('status', 'Due date extended to ' . $newDue->format('M j, Y') . '.');
    }
}
