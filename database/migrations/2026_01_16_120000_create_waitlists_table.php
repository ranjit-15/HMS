<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('waitlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('table_id')->nullable()->constrained('tables')->cascadeOnDelete();
            $table->foreignId('book_id')->nullable()->constrained('books')->cascadeOnDelete();
            $table->string('status', 32)->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['table_id', 'status', 'created_at']);
            $table->index(['book_id', 'status', 'created_at']);
            $table->unique(['user_id', 'table_id'], 'waitlists_user_table_unique');
            $table->unique(['user_id', 'book_id'], 'waitlists_user_book_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waitlists');
    }
};
