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
        Schema::create('battles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('monster_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['in_progress', 'won', 'lost', 'fled'])->default('in_progress');
            $table->integer('user_hp')->default(100);
            $table->integer('monster_hp')->default(100);
            $table->integer('turns')->default(0)->comment('Số lượt đánh');
            $table->json('battle_log')->nullable()->comment('Lịch sử trận đấu');
            $table->integer('xp_gained')->default(0);
            $table->integer('currency_gained')->default(0);
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battles');
    }
};
