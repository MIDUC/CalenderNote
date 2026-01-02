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
        Schema::create('level_names', function (Blueprint $table) {
            $table->id();
            $table->integer('level_min')->comment('Cấp tối thiểu (ví dụ: 1, 11, 21...)');
            $table->integer('level_max')->comment('Cấp tối đa (ví dụ: 10, 20, 30...)');
            $table->string('name')->comment('Tên cấp (ví dụ: Tân thủ, Trung cấp, Cao thủ...)');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->unique(['level_min', 'level_max']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_names');
    }
};
