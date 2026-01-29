<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BorrowRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return Redirect::route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $remember = $data['remember'] ?? false;
        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'admin',
        ];

        if (!Auth::guard('admin')->attempt($credentials, $remember)) {
            return back()->withErrors([
                'auth' => 'Invalid credentials or insufficient permissions.',
            ])->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return Redirect::intended(route('admin.dashboard'));
    }

    public function dashboard()
    {
        $activeBookingsCount = Booking::whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('end_at', '>', now())
            ->count();

        $borrowedCount = BorrowRequest::where('status', 'borrowed')->count();

        $overdueCount = BorrowRequest::where('status', 'borrowed')
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->count();

        $popular = BorrowRequest::select('book_id', DB::raw('count(*) as total'))
            ->groupBy('book_id')
            ->orderByDesc('total')
            ->with('book')
            ->first();

        // Get active table bookings with time remaining
        $activeBookings = Booking::whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('end_at', '>', now())
            ->with(['user', 'table'])
            ->orderBy('end_at')
            ->limit(10)
            ->get();

        // Get currently borrowed books with due dates
        $borrowedBooks = BorrowRequest::where('status', 'borrowed')
            ->with(['user', 'book'])
            ->orderBy('due_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', [
            'activeBookingsCount' => $activeBookingsCount,
            'borrowedCount' => $borrowedCount,
            'overdueCount' => $overdueCount,
            'popularBookTitle' => $popular?->book?->title ?? 'N/A',
            'popularBookBorrows' => $popular->total ?? 0,
            'activeBookings' => $activeBookings,
            'borrowedBooks' => $borrowedBooks,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect with cache-control headers to prevent back-button access
        return Redirect::route('admin.login')
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
    }
}
