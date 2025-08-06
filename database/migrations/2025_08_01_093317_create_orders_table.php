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
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->comment('個人訂單 ID');
            $table->foreignId('group_order_id')->comment('所屬團購單 ID')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->comment('下訂學生 ID')->constrained()->cascadeOnDelete();
            $table->decimal('total_price', 8, 2)->comment('個人訂單總金額');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
