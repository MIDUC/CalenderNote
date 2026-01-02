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
        // Drop tables in correct order (respecting foreign keys)
        Schema::dropIfExists('user_daily_quests');
        Schema::dropIfExists('daily_quests');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('battles');
        Schema::dropIfExists('user_quests');
        Schema::dropIfExists('quests');
        Schema::dropIfExists('monsters');
        Schema::dropIfExists('npcs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This migration only drops tables, so down() is intentionally empty
        // If you need to restore, you would need to re-run the original migrations
    }
};

