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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['consumable', 'equipment', 'material', 'special'])->default('consumable');
            $table->integer('price')->default(0)->comment('Giá mua (linh thạch)');
            $table->integer('sell_price')->default(0)->comment('Giá bán (linh thạch)');
            $table->json('effects')->nullable()->comment('Hiệu ứng (ví dụ: {"exp_bonus": 10, "currency_bonus": 50})');
            $table->string('icon')->nullable()->comment('Icon hoặc emoji');
            $table->integer('rarity')->default(1)->comment('Độ hiếm: 1=Common, 2=Uncommon, 3=Rare, 4=Epic, 5=Legendary');
            $table->timestamps();
        });
        
        // Bảng lưu vật phẩm của user
        Schema::create('user_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->timestamps();
            
            $table->unique(['user_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_items');
        Schema::dropIfExists('items');
    }
};
