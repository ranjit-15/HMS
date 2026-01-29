<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('category')->nullable()->after('isbn');
            $table->string('cover_image')->nullable()->after('category');
            $table->text('description')->nullable()->after('cover_image');
            $table->string('location')->nullable()->after('description')->comment('Shelf/rack location in library');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['category', 'cover_image', 'description', 'location']);
        });
    }
};
