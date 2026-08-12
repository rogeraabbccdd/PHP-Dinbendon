<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('group_orders', function (Blueprint $table) {
            $table->boolean('on_time')->nullable()->comment('是否於 11:50 前送達');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_orders', function (Blueprint $table) {
            $table->dropColumn('on_time');
        });
    }
};
