<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GameRate;

class GameRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            [
                'key' => 'base_critical_rate',
                'name' => 'Tỉ lệ chí mạng cơ bản',
                'description' => 'Tỉ lệ chí mạng mặc định cho user (5%)',
                'value' => 0.05,
                'type' => 'percentage',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'key' => 'base_critical_damage',
                'name' => 'Hệ số sát thương chí mạng cơ bản',
                'description' => 'Hệ số nhân sát thương khi chí mạng (2.0 = 200%)',
                'value' => 2.0,
                'type' => 'multiplier',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'key' => 'base_dodge_rate',
                'name' => 'Tỉ lệ né tránh cơ bản',
                'description' => 'Tỉ lệ né tránh mặc định cho user (3%)',
                'value' => 0.03,
                'type' => 'percentage',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'key' => 'base_drop_rate',
                'name' => 'Tỉ lệ rơi đồ cơ bản',
                'description' => 'Tỉ lệ rơi đồ mặc định (100%)',
                'value' => 1.0,
                'type' => 'percentage',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'key' => 'rare_drop_base_rate',
                'name' => 'Tỉ lệ rơi đồ hiếm cơ bản',
                'description' => 'Tỉ lệ rơi đồ hiếm mặc định (1%)',
                'value' => 0.01,
                'type' => 'percentage',
                'is_active' => true,
                'order' => 5,
            ],
            [
                'key' => 'boss_drop_multiplier',
                'name' => 'Hệ số nhân rơi đồ Boss',
                'description' => 'Boss có tỉ lệ rơi đồ cao hơn (2.0 = 200%)',
                'value' => 2.0,
                'type' => 'multiplier',
                'is_active' => true,
                'order' => 6,
            ],
            [
                'key' => 'encounter_base_rate',
                'name' => 'Tỉ lệ gặp quái cơ bản',
                'description' => 'Tỉ lệ gặp quái khi explore (100%)',
                'value' => 1.0,
                'type' => 'percentage',
                'is_active' => true,
                'order' => 7,
            ],
            [
                'key' => 'status_effect_base_chance',
                'name' => 'Tỉ lệ hiệu ứng trạng thái cơ bản',
                'description' => 'Tỉ lệ gây hiệu ứng trạng thái (10%)',
                'value' => 0.10,
                'type' => 'percentage',
                'is_active' => true,
                'order' => 8,
            ],
        ];

        foreach ($rates as $rate) {
            GameRate::firstOrCreate(['key' => $rate['key']], $rate);
        }
    }
}
