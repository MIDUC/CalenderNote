<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            [
                'name' => 'Bắt đầu hành trình',
                'description' => 'Hoàn thành 10 task đầu tiên',
                'icon' => '🎯',
                'type' => 'task',
                'requirements' => ['task_count' => 10],
                'xp_reward' => 50,
                'currency_reward' => 25,
                'rarity' => 1,
                'order' => 1,
            ],
            [
                'name' => 'Chiến binh dày dạn',
                'description' => 'Hoàn thành 100 task',
                'icon' => '⚔️',
                'type' => 'task',
                'requirements' => ['task_count' => 100],
                'xp_reward' => 500,
                'currency_reward' => 250,
                'rarity' => 3,
                'order' => 2,
            ],
            [
                'name' => 'Lên cấp 10',
                'description' => 'Đạt cấp 10',
                'icon' => '⭐',
                'type' => 'level',
                'requirements' => ['level' => 10],
                'xp_reward' => 100,
                'currency_reward' => 50,
                'rarity' => 2,
                'order' => 3,
            ],
            [
                'name' => 'Triệu phú',
                'description' => 'Sở hữu 10,000 linh thạch',
                'icon' => '💰',
                'type' => 'currency',
                'requirements' => ['currency' => 10000],
                'xp_reward' => 200,
                'currency_reward' => 100,
                'rarity' => 3,
                'order' => 4,
            ],
            [
                'name' => 'Sát thủ quái vật',
                'description' => 'Đánh bại 50 quái vật',
                'icon' => '🗡️',
                'type' => 'battle',
                'requirements' => ['monster_kills' => 50],
                'xp_reward' => 300,
                'currency_reward' => 150,
                'rarity' => 3,
                'order' => 5,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::firstOrCreate(['name' => $achievement['name']], $achievement);
        }
    }
}
