<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LevelName;

class LevelNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levelNames = [
            ['level_min' => 1, 'level_max' => 10, 'name' => 'Tân thủ', 'description' => 'Người mới bắt đầu'],
            ['level_min' => 11, 'level_max' => 20, 'name' => 'Học viên', 'description' => 'Đang học hỏi'],
            ['level_min' => 21, 'level_max' => 30, 'name' => 'Thực tập sinh', 'description' => 'Bắt đầu thực hành'],
            ['level_min' => 31, 'level_max' => 40, 'name' => 'Chuyên viên', 'description' => 'Có kinh nghiệm'],
            ['level_min' => 41, 'level_max' => 50, 'name' => 'Chuyên gia', 'description' => 'Rất giỏi'],
            ['level_min' => 51, 'level_max' => 60, 'name' => 'Bậc thầy', 'description' => 'Thành thạo'],
            ['level_min' => 61, 'level_max' => 70, 'name' => 'Đại sư', 'description' => 'Cực kỳ xuất sắc'],
            ['level_min' => 71, 'level_max' => 80, 'name' => 'Tôn giả', 'description' => 'Bậc cao thủ'],
            ['level_min' => 81, 'level_max' => 90, 'name' => 'Thánh nhân', 'description' => 'Gần như hoàn hảo'],
            ['level_min' => 91, 'level_max' => 100, 'name' => 'Thần', 'description' => 'Đỉnh cao tuyệt đối'],
        ];

        foreach ($levelNames as $levelName) {
            LevelName::firstOrCreate(
                ['level_min' => $levelName['level_min'], 'level_max' => $levelName['level_max']],
                $levelName
            );
        }
    }
}
