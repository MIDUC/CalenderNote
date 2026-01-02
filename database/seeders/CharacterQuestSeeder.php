<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StoryCharacter;
use App\Models\CharacterQuest;

class CharacterQuestSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy các nhân vật
        $linFan = StoryCharacter::where('name', 'Lâm Phàm')->first();
        $wangTian = StoryCharacter::where('name', 'Vương Thiên')->first();
        $liXue = StoryCharacter::where('name', 'Lý Tuyết')->first();
        $zhangWuji = StoryCharacter::where('name', 'Trương Vô Kỵ')->first();
        
        $suzuki = StoryCharacter::where('name', 'Suzuki Taro')->first();
        $elise = StoryCharacter::where('name', 'Elise')->first();
        $valdris = StoryCharacter::where('name', 'Duke Valdris')->first();
        $marcus = StoryCharacter::where('name', 'Marcus')->first();

        // Nhiệm vụ của Lâm Phàm (nhân vật chính)
        if ($linFan && $wangTian && $liXue) {
            CharacterQuest::firstOrCreate(
                ['character_id' => $linFan->id, 'title' => 'Tu luyện cơ bản'],
                [
                    'description' => 'Hoàn thành 10 bài tập luyện tập để tăng cường thể chất',
                    'type' => 'exercise',
                    'requirements' => ['exercise_count' => 10],
                    'target_count' => 10,
                    'xp_reward' => 100,
                    'currency_reward' => 50,
                    'level_required' => 1,
                    'relationship_effects' => [
                        'ally_ids' => [$liXue->id], // Trở thành đồng minh với Lý Tuyết
                    ],
                    'relationship_value_change' => 20,
                    'order' => 1,
                ]
            );

            CharacterQuest::firstOrCreate(
                ['character_id' => $linFan->id, 'title' => 'Thách thức Vương Thiên'],
                [
                    'description' => 'Hoàn thành 20 task để chứng minh sức mạnh, nhưng sẽ trở thành kẻ thù của Vương Thiên',
                    'type' => 'task',
                    'requirements' => ['task_count' => 20],
                    'target_count' => 20,
                    'xp_reward' => 200,
                    'currency_reward' => 100,
                    'level_required' => 5,
                    'relationship_effects' => [
                        'enemy_ids' => [$wangTian->id], // Trở thành kẻ thù với Vương Thiên
                    ],
                    'relationship_value_change' => -50,
                    'order' => 2,
                ]
            );
        }

        // Nhiệm vụ của Vương Thiên (kẻ thù) - Nếu không hoàn thành sẽ bị trừ rất nặng
        if ($wangTian) {
            CharacterQuest::firstOrCreate(
                ['character_id' => $wangTian->id, 'title' => 'Nhiệm vụ bắt buộc từ Vương Thiên'],
                [
                    'description' => 'Vương Thiên yêu cầu bạn hoàn thành 15 task trong 3 ngày. Nếu không hoàn thành, sẽ bị trừ rất nặng!',
                    'type' => 'task',
                    'requirements' => ['task_count' => 15, 'days' => 3],
                    'target_count' => 15,
                    'xp_reward' => 150,
                    'currency_reward' => 75,
                    'level_required' => 3,
                    'relationship_effects' => [
                        'enemy_ids' => [], // Không thay đổi quan hệ nếu hoàn thành
                    ],
                    'relationship_value_change' => 10, // Cải thiện một chút nếu hoàn thành
                    'order' => 1,
                ]
            );
        }

        // Nhiệm vụ của Suzuki
        if ($suzuki && $valdris && $marcus) {
            CharacterQuest::firstOrCreate(
                ['character_id' => $suzuki->id, 'title' => 'Học ma pháp cơ bản'],
                [
                    'description' => 'Hoàn thành 5 bài tập để học ma pháp',
                    'type' => 'exercise',
                    'requirements' => ['exercise_count' => 5],
                    'target_count' => 5,
                    'xp_reward' => 80,
                    'currency_reward' => 40,
                    'level_required' => 1,
                    'relationship_effects' => [
                        'ally_ids' => [$marcus->id],
                    ],
                    'relationship_value_change' => 15,
                    'order' => 1,
                ]
            );

            CharacterQuest::firstOrCreate(
                ['character_id' => $suzuki->id, 'title' => 'Bảo vệ Elise'],
                [
                    'description' => 'Hoàn thành 30 task để bảo vệ Elise, nhưng sẽ trở thành kẻ thù của Duke Valdris',
                    'type' => 'task',
                    'requirements' => ['task_count' => 30],
                    'target_count' => 30,
                    'xp_reward' => 300,
                    'currency_reward' => 150,
                    'level_required' => 10,
                    'relationship_effects' => [
                        'enemy_ids' => [$valdris->id],
                        'ally_ids' => [$elise->id ?? null],
                    ],
                    'relationship_value_change' => -60, // Trở thành kẻ thù với Valdris
                    'order' => 2,
                ]
            );
        }

        // Nhiệm vụ của Duke Valdris (kẻ thù) - Phạt rất nặng nếu không hoàn thành
        if ($valdris) {
            CharacterQuest::firstOrCreate(
                ['character_id' => $valdris->id, 'title' => 'Nhiệm vụ bắt buộc từ Duke Valdris'],
                [
                    'description' => 'Duke Valdris yêu cầu bạn hoàn thành 25 task trong 5 ngày. Nếu không hoàn thành, sẽ bị trừ XP và linh thạch rất nặng!',
                    'type' => 'task',
                    'requirements' => ['task_count' => 25, 'days' => 5],
                    'target_count' => 25,
                    'xp_reward' => 250,
                    'currency_reward' => 125,
                    'level_required' => 8,
                    'relationship_effects' => [],
                    'relationship_value_change' => 5,
                    'order' => 1,
                ]
            );
        }
    }
}

