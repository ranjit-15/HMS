<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->string('check_in_secret', 64)->nullable()->after('is_active')->unique();
        });

        // Backfill existing rows with a secret
        $tables = DB::table('tables')->select('id', 'check_in_secret')->get();
        foreach ($tables as $row) {
            if (! $row->check_in_secret) {
                DB::table('tables')
                    ->where('id', $row->id)
                    ->update(['check_in_secret' => Str::random(40)]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn('check_in_secret');
        });
    }
};
