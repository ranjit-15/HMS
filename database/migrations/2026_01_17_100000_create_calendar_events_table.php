<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('all_day')->default(true);
            $table->string('color', 7)->default('#dc2626'); // Hex color for calendar display
            $table->enum('type', ['event', 'holiday', 'exam', 'deadline', 'other'])->default('event');
            $table->boolean('is_visible')->default(true);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['start_date', 'end_date']);
            $table->index('is_visible');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
