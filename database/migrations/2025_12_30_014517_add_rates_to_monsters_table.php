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
        Schema::table('monsters', function (Blueprint $table) {
            // Critical hit rates
            $table->decimal('critical_rate', 5, 4)->default(0.05)->after('defense')->comment('Tỉ lệ chí mạng (0.05 = 5%)');
            $table->decimal('critical_damage_multiplier', 5, 2)->default(2.0)->after('critical_rate')->comment('Hệ số sát thương chí mạng (2.0 = 200%)');
            
            // Dodge/Evasion rates
            $table->decimal('dodge_rate', 5, 4)->default(0.05)->after('critical_damage_multiplier')->comment('Tỉ lệ né tránh (0.05 = 5%)');
            
            // Drop rates
            $table->decimal('drop_rate_multiplier', 5, 2)->default(1.0)->after('dodge_rate')->comment('Hệ số nhân tỉ lệ rơi đồ (1.0 = 100%)');
            $table->decimal('rare_drop_rate', 5, 4)->default(0.01)->after('drop_rate_multiplier')->comment('Tỉ lệ rơi đồ hiếm (0.01 = 1%)');
            
            // Encounter/Appearance rates
            $table->decimal('encounter_rate', 5, 4)->default(1.0)->after('rare_drop_rate')->comment('Tỉ lệ xuất hiện (1.0 = 100%)');
            
            // Status effect rates
            $table->decimal('status_effect_resistance', 5, 4)->default(0.0)->after('encounter_rate')->comment('Kháng hiệu ứng trạng thái (0.0 = 0%)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monsters', function (Blueprint $table) {
            $table->dropColumn([
                'critical_rate',
                'critical_damage_multiplier',
                'dodge_rate',
                'drop_rate_multiplier',
                'rare_drop_rate',
                'encounter_rate',
                'status_effect_resistance',
            ]);
        });
    }
};
