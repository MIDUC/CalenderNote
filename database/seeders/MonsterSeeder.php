<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Monster;

class MonsterSeeder extends Seeder
{
    public function run(): void
    {
        $monsters = [
            [
                'name' => 'Goblin yếu',
                'description' => 'Quái vật yếu nhất trong rừng',
                'icon' => '👺',
                'level' => 1,
                'hp' => 50,
                'max_hp' => 50,
                'attack' => 5,
                'defense' => 2,
                'xp_reward' => 5,
                'currency_reward' => 3,
                'drop_items' => null,
                'rarity' => 1,
                'is_boss' => false,
                'critical_rate' => 0.03,
                'critical_damage_multiplier' => 1.5,
                'dodge_rate' => 0.05,
                'drop_rate_multiplier' => 1.0,
                'rare_drop_rate' => 0.005,
                'encounter_rate' => 0.6,
                'status_effect_resistance' => 0.0,
            ],
            [
                'name' => 'Orc chiến binh',
                'description' => 'Quái vật mạnh hơn',
                'icon' => '👹',
                'level' => 3,
                'hp' => 150,
                'max_hp' => 150,
                'attack' => 15,
                'defense' => 8,
                'xp_reward' => 20,
                'currency_reward' => 10,
                'drop_items' => null,
                'rarity' => 2,
                'is_boss' => false,
                'critical_rate' => 0.05,
                'critical_damage_multiplier' => 2.0,
                'dodge_rate' => 0.08,
                'drop_rate_multiplier' => 1.2,
                'rare_drop_rate' => 0.01,
                'encounter_rate' => 0.3,
                'status_effect_resistance' => 0.1,
            ],
            [
                'name' => 'Rồng lửa',
                'description' => 'Boss mạnh nhất',
                'icon' => '🐉',
                'level' => 10,
                'hp' => 1000,
                'max_hp' => 1000,
                'attack' => 100,
                'defense' => 50,
                'xp_reward' => 200,
                'currency_reward' => 500,
                'drop_items' => [['item_id' => 5, 'chance' => 0.5]],
                'rarity' => 5,
                'is_boss' => true,
                'critical_rate' => 0.15,
                'critical_damage_multiplier' => 3.0,
                'dodge_rate' => 0.20,
                'drop_rate_multiplier' => 2.5,
                'rare_drop_rate' => 0.10,
                'encounter_rate' => 0.1,
                'status_effect_resistance' => 0.5,
            ],
        ];

        foreach ($monsters as $monster) {
            Monster::firstOrCreate(['name' => $monster['name']], $monster);
        }
    }
}
