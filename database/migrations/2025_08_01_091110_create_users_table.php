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
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('學生 ID');
            $table->string('student_id')->unique()->comment('學號');
            $table->string('password')->comment('密碼');
            $table->string('name')->nullable()->comment('姓名');
            $table->foreignId('course_id')->comment('所屬班級 ID')->constrained()->cascadeOnDelete();
            $table->tinyInteger('seat_number')->unsigned()->comment('座號');
            $table->boolean('enabled')->default(true)->comment('帳號是否啟用');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
