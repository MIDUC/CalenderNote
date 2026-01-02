<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quest;
use App\Models\NPC;

class QuestSeeder extends Seeder
{
    public function run(): void
    {
        $npc = NPC::where('name', 'Thầy giáo Minh')->first();
        if (!$npc) return;

        $quests = [
            [
                'npc_id' => $npc->id,
                'title' => 'Nhiệm vụ đầu tiên',
                'description' => 'Hoàn thành 5 task để bắt đầu hành trình của bạn',
                'type' => 'task',
                'requirements' => ['task_count' => 5],
                'target_count' => 5,
                'xp_reward' => 50,
                'currency_reward' => 25,
                'level_required' => 1,
                'status' => 'available',
            ],
            [
                'npc_id' => $npc->id,
                'title' => 'Chiến binh mới',
                'description' => 'Hoàn thành 20 task để chứng minh sự kiên trì',
                'type' => 'task',
                'requirements' => ['task_count' => 20],
                'target_count' => 20,
                'xp_reward' => 200,
                'currency_reward' => 100,
                'level_required' => 3,
                'status' => 'available',
            ],
            [
                'npc_id' => $npc->id,
                'title' => 'Bậc thầy nhiệm vụ',
                'description' => 'Hoàn thành 50 task để trở thành bậc thầy',
                'type' => 'task',
                'requirements' => ['task_count' => 50],
                'target_count' => 50,
                'xp_reward' => 500,
                'currency_reward' => 250,
                'level_required' => 5,
                'status' => 'available',
            ],
        ];

        foreach ($quests as $quest) {
            Quest::firstOrCreate(
                ['title' => $quest['title'], 'npc_id' => $quest['npc_id']],
                $quest
            );
        }
    }
}
