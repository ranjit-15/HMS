<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Ensure the user is authenticated as admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        $webUser = Auth::user();
        if ($webUser && $webUser->role === 'student') {
            return redirect()->route('student.dashboard')->with('error', 'Forbidden');
        }

        return redirect()->route('admin.login')->withErrors(['auth' => 'Admin access required.']);
    }
}
