<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Show the profile page.
     */
    public function show()
    {
        $user = Auth::user();
        $unreadNotificationsCount = \App\Models\Notification::where('user_id', $user->id)->unread()->count();
        
        return view('student.profile.show', [
            'user' => $user,
            'unreadNotificationsCount' => $unreadNotificationsCount,
        ]);
    }

    /**
     * Update profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user->update($validated);

        return back()->with('status', 'Profile updated successfully.');
    }

    /**
     * Upload and update avatar.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:2048'], // 2MB max
        ]);

        $user = Auth::user();

        // Delete old avatar if exists
        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $file = $request->file('avatar');
        
        // Try to use Intervention Image if available for resizing
        if (class_exists('\Intervention\Image\ImageManager')) {
            try {
                $manager = new \Intervention\Image\ImageManager(
                    new \Intervention\Image\Drivers\Gd\Driver()
                );
                $image = $manager->read($file->getRealPath());
                
                // Cover crop to 256x256 (maintains aspect ratio, crops excess)
                $image->cover(256, 256);
                
                // Generate filename
                $filename = 'avatars/' . $user->id . '_' . Str::random(10) . '.jpg';
                
                // Save to storage
                Storage::disk('public')->put($filename, $image->toJpeg(85));
                
                $user->update(['avatar_path' => $filename]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Avatar uploaded successfully.',
                        'avatar_url' => Storage::url($filename),
                    ]);
                }

                return back()->with('status', 'Avatar uploaded successfully.');
            } catch (\Exception $e) {
                // Fall through to basic upload
            }
        }
        
        // Basic upload without image processing
        $filename = $file->store('avatars', 'public');
        $user->update(['avatar_path' => $filename]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded successfully.',
                'avatar_url' => Storage::url($filename),
            ]);
        }

        return back()->with('status', 'Avatar uploaded successfully.');
    }

    /**
     * Remove avatar.
     */
    public function removeAvatar()
    {
        $user = Auth::user();

        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->update(['avatar_path' => null]);

        return back()->with('status', 'Avatar removed.');
    }
}
