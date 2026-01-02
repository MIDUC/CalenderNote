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
        Schema::create('story_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->nullable()->constrained()->onDelete('cascade')->comment('Story này áp dụng rule, null = áp dụng cho tất cả');
            $table->string('name')->comment('Tên rule');
            $table->text('description')->nullable()->comment('Mô tả rule');
            
            // Trigger conditions
            $table->string('trigger_type')->comment('Loại trigger: kill_monster, complete_task, complete_quest, level_up, etc.');
            $table->json('trigger_conditions')->nullable()->comment('Điều kiện trigger: {"monster_id": 1, "count": 30}');
            $table->integer('required_count')->default(1)->comment('Số lần cần thực hiện');
            
            // Target character
            $table->foreignId('target_character_id')->nullable()->constrained('story_characters')->onDelete('cascade')->comment('Nhân vật bị ảnh hưởng');
            
            // Relationship effects
            $table->integer('relationship_value_change')->default(0)->comment('Thay đổi relationship value (-100 đến 100)');
            $table->string('relationship_type_change')->nullable()->comment('Thay đổi relationship type nếu đạt ngưỡng');
            $table->integer('relationship_threshold')->default(100)->comment('Ngưỡng relationship để trigger penalty (ví dụ: -100 = thù hằn 100%)');
            
            // Penalty if threshold reached
            $table->boolean('has_penalty')->default(false)->comment('Có penalty nếu đạt ngưỡng');
            $table->json('penalty_effects')->nullable()->comment('Penalty effects: {"level_down": 1, "xp_loss": 1000, "currency_loss": 500}');
            $table->integer('penalty_due_days')->nullable()->comment('Số ngày để hoàn thành trước khi bị penalty');
            
            // Progress tracking
            $table->boolean('is_cumulative')->default(true)->comment('Tích lũy qua nhiều lần hay reset mỗi lần');
            $table->boolean('reset_on_complete')->default(false)->comment('Reset counter khi hoàn thành');
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['story_id', 'is_active']);
            $table->index(['trigger_type', 'is_active']);
        });
        
        // Table to track user progress on rules
        Schema::create('user_story_rule_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('story_rule_id')->constrained()->onDelete('cascade');
            $table->integer('current_count')->default(0)->comment('Số lần đã thực hiện');
            $table->integer('relationship_value')->default(0)->comment('Giá trị relationship hiện tại với target character');
            $table->boolean('penalty_applied')->default(false)->comment('Đã áp dụng penalty chưa');
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamp('penalty_due_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'story_rule_id']);
            $table->index(['user_id', 'penalty_applied']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_story_rule_progress');
        Schema::dropIfExists('story_rules');
    }
};

