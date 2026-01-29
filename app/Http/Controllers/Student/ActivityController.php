<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BorrowRequest;
use App\Models\Notification;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(): View
    {
        $now = Carbon::now();
        $userId = auth()->id();

        $bookings = Booking::query()
            ->with('table')
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('end_at', '>', $now)
            ->orderBy('start_at')
            ->get();

        $borrows = BorrowRequest::with('book')
            ->where('user_id', $userId)
            ->whereIn('status', ['approved', 'borrowed'])
            ->orderByRaw("CASE WHEN status = 'borrowed' THEN 0 WHEN status = 'approved' THEN 1 ELSE 2 END")
            ->orderByDesc('requested_at')
            ->get();

        $unreadNotificationsCount = Notification::where('user_id', $userId)->unread()->count();

        return view('student.activity.index', [
            'bookings' => $bookings,
            'borrows' => $borrows,
            'unreadNotificationsCount' => $unreadNotificationsCount,
        ]);
    }
}
