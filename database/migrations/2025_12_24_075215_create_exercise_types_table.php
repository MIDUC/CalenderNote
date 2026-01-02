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
        Schema::create('exercise_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên bài tập (ví dụ: Chống đẩy, Chạy bộ)');
            $table->string('unit')->comment('Đơn vị (ví dụ: lần, phút, km)');
            $table->string('icon')->nullable()->comment('Icon hoặc emoji');
            $table->text('description')->nullable()->comment('Mô tả bài tập');
            $table->integer('base_xp')->default(10)->comment('XP cơ bản mỗi lần tập');
            $table->integer('base_currency')->default(5)->comment('Linh thạch cơ bản mỗi lần tập');
            $table->json('targets')->nullable()->comment('Mục tiêu theo level (ví dụ: {"level_1": 10, "level_2": 20})');
            $table->integer('order')->default(0)->comment('Thứ tự hiển thị');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_types');
    }
};
