<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminPushNotification;
use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = AdminNotification::with(['admin', 'targetUser'])
            ->latest()
            ->paginate(20);

        $students = User::where('role', 'student')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.notifications.index', [
            'notifications' => $notifications,
            'students' => $students,
            'types' => AdminNotification::TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'type' => ['required', 'in:info,warning,success,urgent'],
            'target' => ['required', 'in:all,individual'],
            'user_id' => ['required_if:target,individual', 'nullable', 'exists:users,id'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'send_email' => ['nullable', 'boolean'],
        ]);

        $notification = AdminNotification::create([
            'admin_id' => auth('admin')->id(),
            'user_id' => $data['target'] === 'individual' ? $data['user_id'] : null,
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $data['type'],
            'is_broadcast' => $data['target'] === 'all',
            'expires_at' => $data['expires_at'] ?? null,
        ]);

        // Send email if requested
        $emailsSent = 0;
        if ($request->boolean('send_email')) {
            try {
                if ($notification->is_broadcast) {
                    // Send to all students
                    $students = User::where('role', 'student')->get();
                    foreach ($students as $student) {
                        Mail::to($student->email)->queue(new AdminPushNotification($notification));
                        $emailsSent++;
                    }
                } else {
                    // Send to individual user
                    $user = User::find($notification->user_id);
                    if ($user) {
                        Mail::to($user->email)->queue(new AdminPushNotification($notification));
                        $emailsSent++;
                    }
                }
            } catch (\Exception $e) {
                // Log error but don't fail
                \Log::error('Failed to send notification email: ' . $e->getMessage());
            }
        }

        $target = $notification->is_broadcast
            ? 'all students'
            : ($notification->targetUser->name ?? 'user');

        $emailMessage = $emailsSent > 0 ? " ({$emailsSent} email(s) queued)" : '';

        return redirect()->route('admin.notifications.index')
            ->with('status', "Notification sent to {$target} successfully{$emailMessage}.");
    }

    public function destroy(AdminNotification $notification): RedirectResponse
    {
        $notification->delete();

        return redirect()->route('admin.notifications.index')
            ->with('status', 'Notification deleted successfully.');
    }
}
