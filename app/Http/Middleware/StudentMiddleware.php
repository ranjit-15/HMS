<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    /**
     * Ensure the user is an authenticated student.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'student') {
            return redirect()->route('login.google')->withErrors(['auth' => 'Student access required.']);
        }

        return $next($request);
    }
}
