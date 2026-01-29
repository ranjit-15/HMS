<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $settings = [
            'default_booking_duration_minutes' => '120',
            'booking_auto_release_minutes' => '15',
            'default_borrow_duration_days' => '7',
            'max_borrow_duration_days' => '14',
            'max_booking_duration_minutes' => '300',
        ];

        foreach ($settings as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => $now, 'created_at' => $now]
            );
        }
    }
}
