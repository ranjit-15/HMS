<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BorrowRequest;
use App\Models\Favorite;
use App\Models\Notification;
use App\Models\Waitlist;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query()->where('is_active', true);

        // Search functionality
        $search = $request->input('search');
        if ($search) {
            $query->search($search);
        }

        // Category filter
        $category = $request->input('category');
        if ($category && array_key_exists($category, Book::CATEGORIES)) {
            $query->category($category);
        }

        // Availability filter
        $availability = $request->input('availability');
        if ($availability === 'available') {
            $query->where('copies_available', '>', 0);
        }

        // Sorting
        $sort = $request->input('sort', 'title');
        switch ($sort) {
            case 'author':
                $query->orderBy('author');
                break;
            case 'newest':
                $query->orderByDesc('published_at');
                break;
            case 'popular':
                $query->withCount('borrowRequests')->orderByDesc('borrow_requests_count');
                break;
            default:
                $query->orderBy('title');
        }

        $books = $query->paginate(12)->withQueryString();

        $borrowRequests = BorrowRequest::query()
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved', 'borrowed'])
            ->get()
            ->keyBy('book_id');

        $waitlists = Waitlist::query()
            ->where('user_id', auth()->id())
            ->whereNotNull('book_id')
            ->whereIn('status', ['pending', 'notified'])
            ->pluck('status', 'book_id');

        // Get user's favorites
        $favorites = Favorite::where('user_id', auth()->id())
            ->pluck('book_id')
            ->toArray();

        $unreadNotificationsCount = Notification::where('user_id', auth()->id())->unread()->count();

        return view('student.library.index', [
            'books' => $books,
            'borrowRequests' => $borrowRequests,
            'waitlists' => $waitlists,
            'favorites' => $favorites,
            'categories' => Book::CATEGORIES,
            'currentCategory' => $category,
            'currentSearch' => $search,
            'currentSort' => $sort,
            'currentAvailability' => $availability,
            'unreadNotificationsCount' => $unreadNotificationsCount,
        ]);
    }

    public function show(Book $book)
    {
        $borrowRequest = BorrowRequest::query()
            ->where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'approved', 'borrowed'])
            ->first();

        $isFavorited = Favorite::where('user_id', auth()->id())->where('book_id', $book->id)->exists();
        $waitlistStatus = Waitlist::where('user_id', auth()->id())->where('book_id', $book->id)->whereIn('status', ['pending', 'notified'])->value('status');

        $unreadNotificationsCount = Notification::where('user_id', auth()->id())->unread()->count();

        // Approved reviews display (paginated)
        $reviews = $book->approvedReviews()->with('user')->orderByDesc('created_at')->paginate(6);

        // Can the current user submit a review? (must have borrowed at least once)
        $canReview = false;
        $userId = auth()->id();
        if ($userId) {
            $hasBorrowed = BorrowRequest::where('user_id', $userId)
                ->where('book_id', $book->id)
                ->whereIn('status', ['borrowed', 'returned'])
                ->exists();

            $alreadyReviewed = \App\Models\BookReview::where('user_id', $userId)->where('book_id', $book->id)->exists();

            $canReview = $hasBorrowed && !$alreadyReviewed;
        }

        return view('student.library.show', compact('book', 'borrowRequest', 'isFavorited', 'waitlistStatus', 'unreadNotificationsCount', 'reviews', 'canReview'));
    }
}

