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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id()->comment('菜單品項 ID');
            $table->foreignId('store_id')->comment('所屬店家 ID')->constrained()->cascadeOnDelete();
            $table->string('name')->comment('品項名稱');
            $table->decimal('price', 8, 2)->comment('目前的價格');
            $table->boolean('is_available')->default(true)->comment('目前是否供應中');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
