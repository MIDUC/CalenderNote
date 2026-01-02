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
        Schema::table('users', function (Blueprint $table) {
            $table->date('last_login_date')->nullable()->after('last_task_date')->comment('Ngày đăng nhập cuối cùng');
            $table->integer('login_streak')->default(0)->after('last_login_date')->comment('Chuỗi đăng nhập liên tiếp');
            $table->date('last_reward_claimed_date')->nullable()->after('login_streak')->comment('Ngày nhận phần thưởng cuối cùng');
            $table->integer('current_reward_day')->default(0)->after('last_reward_claimed_date')->comment('Ngày phần thưởng hiện tại (1-7)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_login_date', 'login_streak', 'last_reward_claimed_date', 'current_reward_day']);
        });
    }
};
