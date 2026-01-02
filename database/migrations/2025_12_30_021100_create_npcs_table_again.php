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
        if (!Schema::hasTable('npcs')) {
            Schema::create('npcs', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('icon')->nullable()->comment('Emoji hoặc icon');
                $table->enum('type', ['merchant', 'quest_giver', 'trainer', 'guard'])->default('quest_giver');
                $table->json('dialogue')->nullable()->comment('Lời thoại của NPC');
                $table->integer('level_required')->default(1)->comment('Cấp yêu cầu để tương tác');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('npcs');
    }
};

