<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        // Get admin push notifications for this user (individual or broadcast)
        $notifications = AdminNotification::forUser($userId)
            ->with('admin')
            ->latest()
            ->paginate(20);

        // Mark notifications as read
        foreach ($notifications as $notification) {
            $notification->markAsReadBy(auth()->user());
        }

        // Count unread notifications for the header
        $unreadCount = AdminNotification::forUser($userId)
            ->whereDoesntHave('readByUsers', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->count();

        return view('student.notifications.index', [
            'notifications' => $notifications,
            'unreadNotificationsCount' => $unreadCount,
        ]);
    }

    /**
     * Mark all visible notifications as read for the current user.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $userId = auth()->id();

        $notifications = AdminNotification::forUser($userId)
            ->whereDoesntHave('readByUsers', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->get();

        foreach ($notifications as $notification) {
            $notification->markAsReadBy(auth()->user());
        }

        return response()->json(["success" => true, "unread" => 0]);
    }
}
