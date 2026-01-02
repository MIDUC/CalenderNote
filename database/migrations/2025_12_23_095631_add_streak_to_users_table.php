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
            $table->integer('current_streak')->default(0)->after('defense')->comment('Chuỗi ngày hiện tại');
            $table->integer('longest_streak')->default(0)->after('current_streak')->comment('Chuỗi ngày dài nhất');
            $table->date('last_task_date')->nullable()->after('longest_streak')->comment('Ngày hoàn thành task cuối cùng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['current_streak', 'longest_streak', 'last_task_date']);
        });
    }
};
