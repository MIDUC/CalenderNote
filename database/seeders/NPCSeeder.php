<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NPC;

class NPCSeeder extends Seeder
{
    public function run(): void
    {
        $npcs = [
            [
                'name' => 'Thương nhân Lão Vương',
                'description' => 'Người bán hàng giàu có trong làng',
                'icon' => '👨‍💼',
                'type' => 'merchant',
                'dialogue' => [
                    'greeting' => 'Chào mừng đến cửa hàng của tôi!',
                    'farewell' => 'Hẹn gặp lại!',
                ],
                'level_required' => 1,
            ],
            [
                'name' => 'Thầy giáo Minh',
                'description' => 'Người hướng dẫn nhiệm vụ',
                'icon' => '👨‍🏫',
                'type' => 'quest_giver',
                'dialogue' => [
                    'greeting' => 'Bạn có muốn nhận nhiệm vụ không?',
                    'farewell' => 'Chúc may mắn!',
                ],
                'level_required' => 1,
            ],
            [
                'name' => 'Võ sư Hùng',
                'description' => 'Huấn luyện viên võ thuật',
                'icon' => '🥋',
                'type' => 'trainer',
                'dialogue' => [
                    'greeting' => 'Hãy luyện tập chăm chỉ!',
                    'farewell' => 'Tiếp tục cố gắng!',
                ],
                'level_required' => 5,
            ],
        ];

        foreach ($npcs as $npc) {
            NPC::firstOrCreate(['name' => $npc['name']], $npc);
        }
    }
}
