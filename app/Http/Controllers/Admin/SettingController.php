<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $settings = $this->settings();

        return view('admin.settings.edit', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'default_booking_duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
            'booking_auto_release_minutes' => ['required', 'integer', 'min:5', 'max:240'],
            'default_borrow_duration_days' => ['required', 'integer', 'min:1', 'max:60'],
        ]);

        $now = now();
        foreach ($data as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => (string) $value, 'updated_at' => $now, 'created_at' => $now]
            );
        }

        return back()->with('status', 'Settings updated.');
    }

    private function settings(): array
    {
        $keys = [
            'default_booking_duration_minutes' => 120,
            'booking_auto_release_minutes' => 15,
            'default_borrow_duration_days' => 14,
        ];

        $rows = DB::table('settings')->whereIn('key', array_keys($keys))->pluck('value', 'key');

        foreach ($keys as $key => $fallback) {
            $keys[$key] = (int) ($rows[$key] ?? $fallback);
        }

        return $keys;
    }
}
