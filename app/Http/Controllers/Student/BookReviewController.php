<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookReview;
use App\Models\BorrowRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class BookReviewController extends Controller
{
    /**
     * Store a new book review.
     */
    public function store(Request $request, Book $book): RedirectResponse
    {
        $userId = auth()->id();

        // Check if user has borrowed this book
        $hasBorrowed = BorrowRequest::where('user_id', $userId)
            ->where('book_id', $book->id)
            ->whereIn('status', ['borrowed', 'returned'])
            ->exists();

        if (!$hasBorrowed) {
            return back()->with('error', 'You can only review books you have borrowed.');
        }

        // Check if user already reviewed this book
        $existingReview = BookReview::where('user_id', $userId)
            ->where('book_id', $book->id)
            ->exists();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this book.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:1000'],
        ]);

        BookReview::create([
            'user_id' => $userId,
            'book_id' => $book->id,
            'rating' => $validated['rating'],
            'review' => $validated['review'] ?? null,
            'is_approved' => false,
        ]);

        return back()->with('status', 'Thank you for your review! It will be visible after approval.');
    }

    /**
     * Delete own review.
     */
    public function destroy(BookReview $review): RedirectResponse
    {
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }

        $review->delete();

        return back()->with('status', 'Your review has been deleted.');
    }
}
