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
        // Create monsters table
        if (!Schema::hasTable('monsters')) {
            Schema::create('monsters', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('icon')->nullable()->comment('Emoji hoặc icon');
                $table->integer('level')->default(1);
                $table->integer('hp')->default(100)->comment('Máu');
                $table->integer('max_hp')->default(100);
                $table->integer('attack')->default(10)->comment('Tấn công');
                $table->integer('defense')->default(5)->comment('Phòng thủ');
                $table->integer('xp_reward')->default(10)->comment('XP khi đánh bại');
                $table->integer('currency_reward')->default(5)->comment('Linh thạch khi đánh bại');
                $table->json('drop_items')->nullable()->comment('Vật phẩm rơi ra (ví dụ: [{"item_id": 1, "chance": 0.1}])');
                $table->integer('rarity')->default(1)->comment('Độ hiếm: 1=Common, 2=Uncommon, 3=Rare, 4=Epic, 5=Legendary');
                $table->boolean('is_boss')->default(false);
                $table->timestamps();
            });
        }
        
        // Create battles table
        if (!Schema::hasTable('battles')) {
            Schema::create('battles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('monster_id')->constrained()->onDelete('cascade');
                $table->enum('status', ['in_progress', 'won', 'lost', 'fled'])->default('in_progress');
                $table->integer('user_hp')->default(100);
                $table->integer('monster_hp')->default(100);
                $table->integer('turns')->default(0);
                $table->json('battle_log')->nullable();
                $table->integer('xp_gained')->default(0);
                $table->integer('currency_gained')->default(0);
                $table->timestamp('started_at')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battles');
        Schema::dropIfExists('monsters');
    }
};
