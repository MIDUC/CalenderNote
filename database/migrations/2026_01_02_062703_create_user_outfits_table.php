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
        Schema::create('user_outfits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('outfit_id')->constrained()->onDelete('cascade');
            $table->boolean('is_equipped')->default(false)->comment('Đang mặc');
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'outfit_id']);
            $table->index(['user_id', 'is_equipped']);
        });
        
        // Add current_outfit_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('current_outfit_id')->nullable()->after('avatar')->constrained('outfits')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['current_outfit_id']);
            $table->dropColumn('current_outfit_id');
        });
        
        Schema::dropIfExists('user_outfits');
    }
};
