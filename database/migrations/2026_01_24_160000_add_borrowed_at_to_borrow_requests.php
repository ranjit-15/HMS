<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->timestamp('borrowed_at')->nullable()->after('due_at');
        });
    }

    public function down(): void
    {
        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->dropColumn('borrowed_at');
        });
    }
};
