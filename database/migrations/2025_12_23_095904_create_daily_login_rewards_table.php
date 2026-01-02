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
        Schema::create('daily_login_rewards', function (Blueprint $table) {
            $table->id();
            $table->integer('day')->comment('Ngày trong tuần (1-7, 1=Thứ 2)');
            $table->integer('xp_reward')->default(0);
            $table->integer('currency_reward')->default(0);
            $table->foreignId('item_reward_id')->nullable()->constrained('items')->onDelete('set null');
            $table->integer('item_reward_quantity')->default(0);
            $table->text('description')->nullable();
            $table->integer('order')->default(0)->comment('Thứ tự hiển thị');
            $table->timestamps();
            
            $table->unique('day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_login_rewards');
    }
};
