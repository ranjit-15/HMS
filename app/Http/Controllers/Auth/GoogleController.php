<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            Log::error('Google OAuth failed', ['error' => $e->getMessage()]);
            return Redirect::route('login')->withErrors(['auth' => 'Google login failed. Try again.']);
        }

        // Only allow @techspire.com.np emails
        $email = $googleUser->getEmail();
        $allowedDomain = 'techspire.edu.np';

        if (!str_ends_with(strtolower($email), '@' . $allowedDomain)) {
            Log::warning('Unauthorized email domain attempted login', ['email' => $email]);
            return Redirect::route('login')->withErrors([
                'auth' => 'Only @techspire.edu.np email addresses are allowed to login.'
            ]);
        }

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Student',
                'google_id' => $googleUser->getId(),
                'role' => 'student',
                'password' => Hash::make(Str::random(40)),
                'remember_token' => Str::random(10),
                'email_verified_at' => now(),
            ]
        );

        Auth::login($user, true);
        $request->session()->regenerate();

        return Redirect::intended(route('student.dashboard'));
    }

    public function logout(Request $request)
    {
        // Clear remember-me cookie if exists
        $rememberCookie = Auth::getRecallerName();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect with cache-control headers to prevent back-button access
        return Redirect::route('login')
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
    }
}
