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
        Schema::create('level_xp_requirements', function (Blueprint $table) {
            $table->id();
            $table->integer('level')->unique()->comment('Cấp độ (1, 2, 3, ...)');
            $table->integer('xp_required')->comment('XP cần để lên cấp này (từ level-1 lên level)');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_xp_requirements');
    }
};
