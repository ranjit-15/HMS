<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add 'checked_in' to bookings status enum (MySQL). SQLite has no enum.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled', 'expired', 'checked_in') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }
        DB::table('bookings')->where('status', 'checked_in')->update(['status' => 'confirmed']);
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled', 'expired') NOT NULL DEFAULT 'pending'");
    }
};
