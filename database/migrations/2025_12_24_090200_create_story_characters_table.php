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
        Schema::create('story_characters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->constrained()->onDelete('cascade');
            $table->string('name')->comment('Tên nhân vật');
            $table->string('name_en')->nullable()->comment('Tên tiếng Anh');
            $table->text('description')->nullable()->comment('Mô tả nhân vật');
            $table->string('avatar')->nullable()->comment('Ảnh đại diện');
            $table->enum('role', ['protagonist', 'antagonist', 'supporting', 'mentor', 'love_interest', 'villain', 'ally', 'rival'])->default('supporting');
            $table->integer('chapter_appearance')->default(1)->comment('Chương xuất hiện');
            $table->json('attributes')->nullable()->comment('Thuộc tính (ví dụ: {"cultivation_level": "Foundation", "element": "Fire"})');
            $table->json('dialogue')->nullable()->comment('Lời thoại của nhân vật');
            $table->integer('order')->default(0)->comment('Thứ tự hiển thị');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['story_id', 'chapter_appearance']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('story_characters');
    }
};

