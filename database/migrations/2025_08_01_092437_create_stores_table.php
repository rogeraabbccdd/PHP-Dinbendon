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
        Schema::create('stores', function (Blueprint $table) {
            $table->id()->comment('店家 ID');
            $table->string('name')->comment('店家名稱');
            $table->string('address')->nullable()->comment('地址');
            $table->string('google_map')->nullable()->comment('Google 地圖連結');
            $table->string('phone')->nullable()->comment('聯絡電話');
            $table->boolean('is_open')->default(true)->comment('是否營業中');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
