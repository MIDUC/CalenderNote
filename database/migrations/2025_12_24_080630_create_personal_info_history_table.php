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
        Schema::create('personal_info_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('age')->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->string('gender')->nullable();
            $table->decimal('bmi', 4, 1)->nullable()->comment('BMI tại thời điểm này');
            $table->string('month_year')->comment('Tháng/Năm (format: YYYY-MM)');
            $table->text('goals')->nullable()->comment('Mục tiêu của tháng (JSON)');
            $table->text('notes')->nullable()->comment('Ghi chú');
            $table->timestamps();
            
            $table->unique(['user_id', 'month_year']); // Mỗi user chỉ có 1 bản ghi mỗi tháng
            $table->index(['user_id', 'month_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_info_history');
    }
};
