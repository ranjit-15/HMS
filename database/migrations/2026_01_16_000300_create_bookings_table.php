<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('table_id')->constrained('tables')->cascadeOnDelete();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'expired'])->default('pending');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->dateTime('released_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['table_id', 'start_at', 'end_at']);
            $table->index(['user_id', 'start_at']);
        });

        // Enforce end time after start time at the DB level (MySQL 8+ supports CHECK).
        // Skip adding the DB-level CHECK when using SQLite in-memory (tests) which may not
        // support ALTER TABLE ... ADD CONSTRAINT syntax.
        try {
            $driver = DB::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
        } catch (\Throwable $e) {
            $driver = null;
        }

        if ($driver !== 'sqlite') {
            DB::statement('ALTER TABLE bookings ADD CONSTRAINT bookings_end_after_start CHECK (end_at > start_at)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
