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
        Schema::create('daily_quests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['complete_tasks', 'complete_notes', 'complete_quests', 'win_battles', 'earn_currency'])->default('complete_tasks');
            $table->integer('target_count')->default(1)->comment('Số lượng cần hoàn thành');
            $table->integer('xp_reward')->default(20);
            $table->integer('currency_reward')->default(10);
            $table->date('date')->comment('Ngày của daily quest');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('date');
        });
        
        // Bảng lưu daily quests của user
        Schema::create('user_daily_quests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('daily_quest_id')->constrained()->onDelete('cascade');
            $table->integer('progress')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->boolean('is_claimed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'daily_quest_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_daily_quests');
        Schema::dropIfExists('daily_quests');
    }
};
