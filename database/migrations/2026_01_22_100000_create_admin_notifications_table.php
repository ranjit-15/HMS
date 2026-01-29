<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // null = all users
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['info', 'warning', 'success', 'urgent'])->default('info');
            $table->boolean('is_broadcast')->default(false); // true = sent to all users
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['is_broadcast', 'created_at']);
        });

        // Add read tracking for admin notifications
        Schema::create('admin_notification_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_notification_id')->constrained('admin_notifications')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('read_at');

            $table->unique(['admin_notification_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notification_reads');
        Schema::dropIfExists('admin_notifications');
    }
};
