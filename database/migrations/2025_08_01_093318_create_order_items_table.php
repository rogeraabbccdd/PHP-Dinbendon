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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id()->comment('訂單品項 ID');
            $table->foreignId('order_id')->comment('所屬個人訂單 ID')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->comment('所屬菜單品項 ID')->constrained()->cascadeOnDelete();
            $table->string('name')->comment('品項名稱 (快照)');
            $table->decimal('price', 8, 2)->comment('品項單價 (快照)');
            $table->integer('quantity')->comment('購買數量');
            $table->string('comment')->comment('備註');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
