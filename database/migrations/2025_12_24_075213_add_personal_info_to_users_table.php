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
            $table->integer('age')->nullable()->after('current_reward_day')->comment('Tuổi');
            $table->decimal('weight', 5, 2)->nullable()->after('age')->comment('Cân nặng (kg)');
            $table->decimal('height', 5, 2)->nullable()->after('weight')->comment('Chiều cao (cm)');
            $table->string('gender')->nullable()->after('height')->comment('Giới tính');
            $table->text('story_progress')->nullable()->after('gender')->comment('Tiến độ cốt truyện (JSON)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['age', 'weight', 'height', 'gender', 'story_progress']);
        });
    }
};
