<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Favorite;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    /**
     * Display user's favorite books
     */
    public function index(): View
    {
        $favorites = Favorite::with('book')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        $unreadNotificationsCount = Notification::where('user_id', auth()->id())->unread()->count();

        return view('student.favorites.index', [
            'favorites' => $favorites,
            'unreadNotificationsCount' => $unreadNotificationsCount,
        ]);
    }

    /**
     * Toggle favorite status for a book
     */
    public function toggle(Book $book)
    {
        $userId = auth()->id();
        $existing = Favorite::where('user_id', $userId)
            ->where('book_id', $book->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = "'{$book->title}' removed from your favorites.";
            $isFavorited = false;
        } else {
            Favorite::create([
                'user_id' => $userId,
                'book_id' => $book->id,
            ]);
            $message = "'{$book->title}' added to your favorites!";
            $isFavorited = true;
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_favorited' => $isFavorited,
                'message' => $message,
            ]);
        }

        return back()->with('status', $message);
    }
}
