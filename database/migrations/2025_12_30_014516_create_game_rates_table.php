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
        Schema::create('game_rates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Key của tỉ lệ (ví dụ: base_critical_rate, base_dodge_rate)');
            $table->string('name')->comment('Tên tỉ lệ');
            $table->text('description')->nullable()->comment('Mô tả');
            $table->decimal('value', 5, 4)->default(0)->comment('Giá trị tỉ lệ (0.0000 - 1.0000)');
            $table->string('type')->default('percentage')->comment('Loại: percentage, multiplier, fixed');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_rates');
    }
};
