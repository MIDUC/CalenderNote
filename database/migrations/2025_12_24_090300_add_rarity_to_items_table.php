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
        Schema::table('items', function (Blueprint $table) {
            // Items table already has rarity column, just add grade if needed
            if (!Schema::hasColumn('items', 'grade')) {
                $table->integer('grade')->default(1)->after('rarity')->comment('Cấp độ: 1-10');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'grade')) {
                $table->dropColumn('grade');
            }
            if (Schema::hasColumn('items', 'rarity')) {
                $table->dropColumn('rarity');
            }
        });
    }
};

