<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->timestamp('admin_confirmed_at')->nullable()->after('returned_at');
        });
    }

    public function down()
    {
        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->dropColumn('admin_confirmed_at');
        });
    }
};
