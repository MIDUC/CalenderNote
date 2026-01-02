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
        Schema::create('user_character_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('character_id')->constrained('story_characters')->onDelete('cascade');
            $table->enum('relationship_type', ['neutral', 'ally', 'enemy', 'rival', 'friend', 'master'])->default('neutral');
            $table->integer('relationship_value')->default(0)->comment('Giá trị quan hệ: -100 đến 100');
            $table->text('notes')->nullable()->comment('Ghi chú về quan hệ');
            $table->timestamps();
            
            $table->unique(['user_id', 'character_id']);
            $table->index(['user_id', 'relationship_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_character_relationships');
    }
};

