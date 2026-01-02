<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DailyLoginReward;

class DailyLoginRewardSeeder extends Seeder
{
    public function run(): void
    {
        $rewards = [
            [
                'day' => 1,
                'xp_reward' => 10,
                'currency_reward' => 5,
                'item_reward_id' => null,
                'item_reward_quantity' => 0,
                'description' => 'Phần thưởng ngày đầu tiên',
                'order' => 1,
            ],
            [
                'day' => 2,
                'xp_reward' => 15,
                'currency_reward' => 10,
                'item_reward_id' => null,
                'item_reward_quantity' => 0,
                'description' => 'Phần thưởng ngày thứ 2',
                'order' => 2,
            ],
            [
                'day' => 3,
                'xp_reward' => 20,
                'currency_reward' => 15,
                'item_reward_id' => null,
                'item_reward_quantity' => 0,
                'description' => 'Phần thưởng ngày thứ 3',
                'order' => 3,
            ],
            [
                'day' => 4,
                'xp_reward' => 25,
                'currency_reward' => 20,
                'item_reward_id' => null,
                'item_reward_quantity' => 0,
                'description' => 'Phần thưởng ngày thứ 4',
                'order' => 4,
            ],
            [
                'day' => 5,
                'xp_reward' => 30,
                'currency_reward' => 25,
                'item_reward_id' => null,
                'item_reward_quantity' => 0,
                'description' => 'Phần thưởng ngày thứ 5',
                'order' => 5,
            ],
            [
                'day' => 6,
                'xp_reward' => 40,
                'currency_reward' => 30,
                'item_reward_id' => null,
                'item_reward_quantity' => 0,
                'description' => 'Phần thưởng ngày thứ 6',
                'order' => 6,
            ],
            [
                'day' => 7,
                'xp_reward' => 50,
                'currency_reward' => 50,
                'item_reward_id' => null,
                'item_reward_quantity' => 0,
                'description' => 'Phần thưởng ngày thứ 7 - Bonus!',
                'order' => 7,
            ],
        ];

        foreach ($rewards as $reward) {
            DailyLoginReward::firstOrCreate(
                ['day' => $reward['day']],
                $reward
            );
        }
    }
}
