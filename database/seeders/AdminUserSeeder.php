<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        $name = env('ADMIN_NAME', 'HMS Administrator');

        // Validate required environment variables
        if (empty($email)) {
            $this->command->error('ADMIN_EMAIL is not set in .env file. Skipping admin user creation.');
            return;
        }

        if (empty($password)) {
            $this->command->error('ADMIN_PASSWORD is not set in .env file. Skipping admin user creation.');
            return;
        }

        // Warn if password is too short
        if (strlen($password) < 8) {
            $this->command->warn('ADMIN_PASSWORD is less than 8 characters. Consider using a stronger password.');
        }

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'role' => 'admin',
                'password' => Hash::make($password),
                'google_id' => null,
                'remember_token' => Str::random(10),
            ]
        );

        $this->command->info("Admin user '{$admin->name}' ({$admin->email}) created/updated successfully.");
    }
}
