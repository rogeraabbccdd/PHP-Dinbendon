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
        Schema::create('comments', function (Blueprint $table) {
            $table->id()->comment('評論 ID');
            $table->foreignId('user_id')->comment('評論者 ID')->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->comment('店家 ID')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->comment('評分 (1-5)');
            $table->text('content')->nullable()->comment('評論內容');
            $table->timestamps();

            $table->unique(['user_id', 'store_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
