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
        Schema::create('exercise_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_type_id')->constrained()->onDelete('cascade');
            $table->decimal('target_amount', 8, 2)->comment('Mục tiêu (ví dụ: 20 lần, 5km)');
            $table->enum('frequency', ['daily', 'weekly', 'custom'])->default('daily')->comment('Tần suất');
            $table->json('days_of_week')->nullable()->comment('Các ngày trong tuần (nếu weekly)');
            $table->date('start_date')->comment('Ngày bắt đầu');
            $table->date('end_date')->nullable()->comment('Ngày kết thúc (null = không giới hạn)');
            $table->boolean('is_active')->default(true);
            $table->integer('current_streak')->default(0)->comment('Chuỗi ngày hoàn thành');
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_schedules');
    }
};
