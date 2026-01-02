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
        Schema::create('user_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_type_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 8, 2)->comment('Số lượng (ví dụ: 20 lần chống đẩy, 5km chạy)');
            $table->date('exercise_date')->comment('Ngày tập');
            $table->text('notes')->nullable()->comment('Ghi chú');
            $table->integer('xp_gained')->default(0)->comment('XP nhận được');
            $table->integer('currency_gained')->default(0)->comment('Linh thạch nhận được');
            $table->timestamps();
            
            $table->index(['user_id', 'exercise_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_exercises');
    }
};
