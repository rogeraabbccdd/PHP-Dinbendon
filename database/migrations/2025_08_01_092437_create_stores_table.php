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
            $table->string('facebook')->nullable()->comment('Facebook 頁面');
            $table->string('instagram')->nullable()->comment('Instagram 頁面');
            $table->string('business_hours')->nullable()->comment('營業時間');
            $table->string('phone')->comment('聯絡電話');
            $table->string('image')->comment('店家圖片 URL');
            $table->string('delivery_conditions')->nullable()->comment('外送條件');
            $table->boolean('is_closed')->default(true)->comment('是否歇業');
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
