<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BorrowRequest;
use App\Models\Booking;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $role = $request->query('role', '');

        $users = User::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, fn($q) => $q->where('role', $role))
            ->withCount(['borrowRequests', 'bookings'])
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    /**
     * Display user details with activity history.
     */
    public function show(User $user): View
    {
        $user->loadCount(['borrowRequests', 'bookings', 'favorites']);

        $recentBorrows = BorrowRequest::with('book')
            ->where('user_id', $user->id)
            ->orderByDesc('requested_at')
            ->limit(10)
            ->get();

        $recentBookings = Booking::with('table')
            ->where('user_id', $user->id)
            ->orderByDesc('start_at')
            ->limit(10)
            ->get();

        $stats = [
            'total_borrows' => BorrowRequest::where('user_id', $user->id)->count(),
            'active_borrows' => BorrowRequest::where('user_id', $user->id)
                ->where('status', 'borrowed')
                ->count(),
            'overdue_borrows' => BorrowRequest::where('user_id', $user->id)
                ->where('status', 'borrowed')
                ->where('due_at', '<', now())
                ->count(),
            'total_bookings' => Booking::where('user_id', $user->id)->count(),
        ];

        return view('admin.users.show', compact('user', 'recentBorrows', 'recentBookings', 'stats'));
    }

    /**
     * Toggle user ban status.
     */
    public function toggleBan(User $user): RedirectResponse
    {
        $user->update(['is_banned' => !$user->is_banned]);

        $action = $user->is_banned ? 'banned' : 'unbanned';

        AuditLog::create([
            'admin_id' => auth('admin')->id(),
            'action' => $action,
            'target_type' => 'user',
            'target_id' => $user->id,
        ]);

        return back()->with('status', "User has been {$action}.");
    }
}
