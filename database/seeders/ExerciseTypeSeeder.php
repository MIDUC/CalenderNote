<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExerciseType;

class ExerciseTypeSeeder extends Seeder
{
    public function run(): void
    {
        $exercises = [
            [
                'name' => 'Chống đẩy',
                'unit' => 'lần',
                'icon' => '💪',
                'description' => 'Bài tập tăng cường sức mạnh cơ tay và ngực',
                'base_xp' => 15,
                'base_currency' => 8,
                'targets' => ['level_1' => 10, 'level_2' => 20, 'level_3' => 30, 'level_4' => 50],
                'order' => 1,
            ],
            [
                'name' => 'Chạy bộ',
                'unit' => 'km',
                'icon' => '🏃',
                'description' => 'Bài tập cải thiện sức bền và tim mạch',
                'base_xp' => 20,
                'base_currency' => 10,
                'targets' => ['level_1' => 1, 'level_2' => 2, 'level_3' => 3, 'level_4' => 5],
                'order' => 2,
            ],
            [
                'name' => 'Gập bụng',
                'unit' => 'lần',
                'icon' => '🤸',
                'description' => 'Bài tập tăng cường cơ bụng',
                'base_xp' => 12,
                'base_currency' => 6,
                'targets' => ['level_1' => 15, 'level_2' => 30, 'level_3' => 50, 'level_4' => 100],
                'order' => 3,
            ],
            [
                'name' => 'Squat',
                'unit' => 'lần',
                'icon' => '🦵',
                'description' => 'Bài tập tăng cường cơ đùi và mông',
                'base_xp' => 15,
                'base_currency' => 8,
                'targets' => ['level_1' => 10, 'level_2' => 20, 'level_3' => 30, 'level_4' => 50],
                'order' => 4,
            ],
            [
                'name' => 'Plank',
                'unit' => 'phút',
                'icon' => '🧘',
                'description' => 'Bài tập tăng cường sức mạnh cơ core',
                'base_xp' => 18,
                'base_currency' => 9,
                'targets' => ['level_1' => 1, 'level_2' => 2, 'level_3' => 3, 'level_4' => 5],
                'order' => 5,
            ],
        ];

        foreach ($exercises as $exercise) {
            ExerciseType::firstOrCreate(
                ['name' => $exercise['name']],
                $exercise
            );
        }
    }
}
