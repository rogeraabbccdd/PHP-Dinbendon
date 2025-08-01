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
        Schema::create('group_orders', function (Blueprint $table) {
            $table->id()->comment('團購單 ID');
            $table->foreignId('course_id')->comment('開團班級 ID')->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->comment('訂購店家 ID')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->comment('開團者 ID')->constrained()->cascadeOnDelete();
            $table->enum('status', ['open', 'closed', 'ordered'])->default('open')->comment('團購單狀態');
            $table->json('menu_snapshot')->comment('開團時的菜單快照');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_orders');
    }
};
