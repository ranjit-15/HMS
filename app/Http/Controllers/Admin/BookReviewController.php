<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookReview;
use App\Models\AuditLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookReviewController extends Controller
{
    /**
     * Display a listing of all book reviews for moderation.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');
        
        $reviews = BookReview::with(['book', 'user'])
            ->when($status === 'pending', fn($q) => $q->where('is_approved', false))
            ->when($status === 'approved', fn($q) => $q->where('is_approved', true))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Approve a book review.
     */
    public function approve(BookReview $review): RedirectResponse
    {
        $review->update(['is_approved' => true]);

        $this->log('approved', 'book_review', $review->id);

        Notification::create([
            'user_id' => $review->user_id,
            'type' => 'review_approved',
            'message' => "Your review for \"{$review->book->title}\" has been approved.",
            'target_type' => 'book_review',
            'target_id' => $review->id,
        ]);

        return back()->with('status', 'Review approved successfully.');
    }

    /**
     * Reject (delete) a book review.
     */
    public function reject(BookReview $review): RedirectResponse
    {
        $bookTitle = $review->book->title;
        $bookId = $review->book_id;
        $userId = $review->user_id;
        $reviewId = $review->id;

        $review->delete();

        $this->log('rejected', 'book_review', $reviewId);

        Notification::create([
            'user_id' => $userId,
            'type' => 'review_rejected',
            'message' => "Your review for \"{$bookTitle}\" was not approved.",
            'target_type' => 'book',
            'target_id' => $bookId,
        ]);

        return back()->with('status', 'Review rejected and deleted.');
    }

    /**
     * Log admin actions
     */
    private function log(string $action, string $targetType, ?int $targetId = null): void
    {
        AuditLog::create([
            'admin_id' => auth('admin')->id(),
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
        ]);
    }
}
