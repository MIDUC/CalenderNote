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
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Tên cốt truyện');
            $table->string('title_en')->nullable()->comment('Tên tiếng Anh');
            $table->text('description')->nullable()->comment('Mô tả cốt truyện');
            $table->string('source')->default('custom')->comment('Nguồn: custom, china, japan, korea');
            $table->string('genre')->nullable()->comment('Thể loại: xianxia, wuxia, cultivation, isekai, etc.');
            $table->string('cover_image')->nullable()->comment('Ảnh bìa');
            $table->integer('total_chapters')->default(0)->comment('Tổng số chương');
            $table->integer('unlock_level')->default(1)->comment('Cấp độ mở khóa');
            $table->integer('order')->default(0)->comment('Thứ tự hiển thị');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};

