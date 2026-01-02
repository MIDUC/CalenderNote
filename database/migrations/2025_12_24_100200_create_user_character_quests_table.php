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
        Schema::create('user_character_quests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('character_quest_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['available', 'in_progress', 'completed', 'failed', 'expired'])->default('available');
            $table->integer('progress')->default(0);
            $table->date('accepted_at')->nullable();
            $table->date('due_date')->nullable()->comment('Hạn chót hoàn thành');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->integer('xp_penalty')->default(0)->comment('XP bị trừ nếu thất bại (đặc biệt với kẻ thù)');
            $table->integer('currency_penalty')->default(0)->comment('Linh thạch bị trừ nếu thất bại');
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_character_quests');
    }
};

