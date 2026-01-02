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
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('exp')->default(0)->after('role');
            $table->integer('level')->default(1)->after('exp');
            $table->bigInteger('currency')->default(0)->comment('Linh thạch')->after('level');
            $table->string('level_name')->nullable()->after('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['exp', 'level', 'currency', 'level_name']);
        });
    }
};
