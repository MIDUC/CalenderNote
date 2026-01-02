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
        Schema::create('outfits', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên trang phục');
            $table->string('name_en')->nullable()->comment('Tên tiếng Anh');
            $table->text('description')->nullable()->comment('Mô tả');
            $table->string('category')->default('casual')->comment('Loại: casual, formal, battle, cultivation, special');
            $table->string('icon')->nullable()->comment('Icon trang phục (emoji hoặc URL)');
            $table->string('sprite_url')->nullable()->comment('URL hình ảnh sprite của trang phục');
            $table->json('sprite_config')->nullable()->comment('Cấu hình sprite: {frames: [], animations: {}}');
            $table->integer('price')->default(0)->comment('Giá mua (currency)');
            $table->integer('price_type')->default(0)->comment('0 = currency, 1 = premium currency');
            $table->integer('level_required')->default(1)->comment('Cấp độ yêu cầu');
            $table->json('stats_bonus')->nullable()->comment('Bonus stats: {hp: 10, attack: 5, defense: 3}');
            $table->json('effects')->nullable()->comment('Hiệu ứng đặc biệt');
            $table->boolean('is_premium')->default(false)->comment('Trang phục premium');
            $table->boolean('is_limited')->default(false)->comment('Trang phục giới hạn');
            $table->timestamp('limited_until')->nullable()->comment('Hết hạn vào');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['category', 'is_active']);
            $table->index(['is_premium', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outfits');
    }
};
