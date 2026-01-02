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
        Schema::create('character_quests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->constrained('story_characters')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['task', 'exercise', 'collect', 'defeat', 'explore', 'social'])->default('task');
            $table->json('requirements')->nullable()->comment('Yêu cầu (ví dụ: {"task_count": 5, "exercise_type_id": 1})');
            $table->integer('target_count')->default(1);
            $table->integer('xp_reward')->default(0);
            $table->integer('currency_reward')->default(0);
            $table->foreignId('item_reward_id')->nullable()->constrained('items')->onDelete('set null');
            $table->integer('item_reward_quantity')->default(0);
            $table->integer('level_required')->default(1);
            $table->json('relationship_effects')->nullable()->comment('Ảnh hưởng quan hệ: {"enemy_ids": [2,3], "ally_ids": [4]}');
            $table->integer('relationship_value_change')->default(0)->comment('Thay đổi giá trị quan hệ khi hoàn thành');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['character_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_quests');
    }
};

