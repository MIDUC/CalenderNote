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
        Schema::create('quests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('npc_id')->nullable()->constrained('npcs')->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['task', 'kill', 'collect', 'explore', 'daily'])->default('task');
            $table->json('requirements')->nullable()->comment('Yêu cầu (ví dụ: {"task_count": 5, "monster_id": 1, "item_id": 2})');
            $table->integer('target_count')->default(1)->comment('Số lượng cần hoàn thành');
            $table->integer('current_count')->default(0)->comment('Số lượng hiện tại');
            $table->integer('xp_reward')->default(0);
            $table->integer('currency_reward')->default(0);
            $table->foreignId('item_reward_id')->nullable()->constrained('items')->onDelete('set null');
            $table->integer('item_reward_quantity')->default(0);
            $table->integer('level_required')->default(1);
            $table->enum('status', ['available', 'in_progress', 'completed', 'claimed'])->default('available');
            $table->timestamps();
        });
        
        // Bảng lưu quests của user
        Schema::create('user_quests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('quest_id')->constrained()->onDelete('cascade');
            $table->integer('progress')->default(0)->comment('Tiến độ hoàn thành');
            $table->enum('status', ['in_progress', 'completed', 'claimed'])->default('in_progress');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'quest_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_quests');
        Schema::dropIfExists('quests');
    }
};
