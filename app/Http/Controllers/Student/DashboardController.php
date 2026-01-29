<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BorrowRequest;
use App\Models\Favorite;
use App\Models\Notification;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard with quick stats
     */
    public function index(): View
    {
        $userId = auth()->id();

        // Get unread notifications count
        $unreadNotificationsCount = Notification::where('user_id', $userId)->unread()->count();

        // Get quick stats for the dashboard
        $stats = [
            'activeBookings' => Booking::where('user_id', $userId)
                ->where('status', 'confirmed')
                ->where('end_at', '>', now())
                ->count(),

            'pendingBookings' => Booking::where('user_id', $userId)
                ->where('status', 'pending')
                ->count(),

            'borrowedBooks' => BorrowRequest::where('user_id', $userId)
                ->where('status', 'borrowed')
                ->count(),

            'pendingBorrows' => BorrowRequest::where('user_id', $userId)
                ->whereIn('status', ['pending', 'approved'])
                ->count(),

            'overdueBooks' => BorrowRequest::where('user_id', $userId)
                ->where('status', 'borrowed')
                ->where('due_at', '<', now())
                ->count(),

            'favorites' => Favorite::where('user_id', $userId)->count(),

            'totalBookings' => Booking::where('user_id', $userId)->count(),
            'totalBorrows' => BorrowRequest::where('user_id', $userId)->count(),
        ];

        // Get recent activity
        $recentBookings = Booking::with('table')
            ->where('user_id', $userId)
            ->latest()
            ->take(3)
            ->get();

        $recentBorrows = BorrowRequest::with('book')
            ->where('user_id', $userId)
            ->latest()
            ->take(3)
            ->get();

        return view('student.dashboard', [
            'unreadNotificationsCount' => $unreadNotificationsCount,
            'stats' => $stats,
            'recentBookings' => $recentBookings,
            'recentBorrows' => $recentBorrows,
        ]);
    }
}
