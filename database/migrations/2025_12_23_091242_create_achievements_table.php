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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('icon')->nullable()->comment('Emoji hoặc icon');
            $table->enum('type', ['task', 'level', 'currency', 'quest', 'battle', 'streak', 'special'])->default('task');
            $table->json('requirements')->nullable()->comment('Yêu cầu (ví dụ: {"task_count": 100, "level": 10})');
            $table->integer('xp_reward')->default(0);
            $table->integer('currency_reward')->default(0);
            $table->integer('rarity')->default(1)->comment('Độ hiếm: 1=Common, 2=Uncommon, 3=Rare, 4=Epic, 5=Legendary');
            $table->integer('order')->default(0)->comment('Thứ tự hiển thị');
            $table->timestamps();
        });
        
        // Bảng lưu achievements của user
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('achievement_id')->constrained()->onDelete('cascade');
            $table->integer('progress')->default(0)->comment('Tiến độ đạt được');
            $table->boolean('is_unlocked')->default(false);
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'achievement_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievements');
    }
};
