<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LevelName;
use Illuminate\Support\Facades\DB;

class LevelNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Cảnh giới tu tiên theo phong cách xianxia
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('level_names')->truncate();

        $levelNames = [
            // Luyện Khí Kỳ (Level 1-10)
            [
                'level_min' => 1,
                'level_max' => 10,
                'name' => 'Luyện Khí Kỳ',
                'description' => 'Giai đoạn đầu của tu tiên, luyện khí trong cơ thể',
            ],
            // Trúc Cơ Kỳ (Level 11-20)
            [
                'level_min' => 11,
                'level_max' => 20,
                'name' => 'Trúc Cơ Kỳ',
                'description' => 'Xây dựng nền tảng vững chắc cho tu tiên',
            ],
            // Kim Đan Kỳ (Level 21-30)
            [
                'level_min' => 21,
                'level_max' => 30,
                'name' => 'Kim Đan Kỳ',
                'description' => 'Ngưng tụ linh khí thành kim đan trong đan điền',
            ],
            // Nguyên Anh Kỳ (Level 31-40)
            [
                'level_min' => 31,
                'level_max' => 40,
                'name' => 'Nguyên Anh Kỳ',
                'description' => 'Kim đan hóa thành nguyên anh, linh thức mạnh mẽ hơn',
            ],
            // Hóa Thần Kỳ (Level 41-50)
            [
                'level_min' => 41,
                'level_max' => 50,
                'name' => 'Hóa Thần Kỳ',
                'description' => 'Nguyên anh hóa thần, linh thức đạt đến cảnh giới cao',
            ],
            // Luyện Hư Kỳ (Level 51-60)
            [
                'level_min' => 51,
                'level_max' => 60,
                'name' => 'Luyện Hư Kỳ',
                'description' => 'Luyện hư hóa thực, bước đầu tiếp xúc với đạo',
            ],
            // Hợp Thể Kỳ (Level 61-70)
            [
                'level_min' => 61,
                'level_max' => 70,
                'name' => 'Hợp Thể Kỳ',
                'description' => 'Hợp nhất với thiên địa, đạt đến cảnh giới cao cấp',
            ],
            // Đại Thừa Kỳ (Level 71-80)
            [
                'level_min' => 71,
                'level_max' => 80,
                'name' => 'Đại Thừa Kỳ',
                'description' => 'Cảnh giới đại thừa, gần như bất tử',
            ],
            // Độ Kiếp Kỳ (Level 81-90)
            [
                'level_min' => 81,
                'level_max' => 90,
                'name' => 'Độ Kiếp Kỳ',
                'description' => 'Vượt qua thiên kiếp, chuẩn bị phi thăng',
            ],
            // Địa Tiên (Level 91-100)
            [
                'level_min' => 91,
                'level_max' => 100,
                'name' => 'Địa Tiên',
                'description' => 'Đạt đến cảnh giới địa tiên, sức mạnh vượt trội',
            ],
            // Thiên Tiên (Level 101-110)
            [
                'level_min' => 101,
                'level_max' => 110,
                'name' => 'Thiên Tiên',
                'description' => 'Cảnh giới thiên tiên, có thể phi thăng',
            ],
            // Kim Tiên (Level 111-120)
            [
                'level_min' => 111,
                'level_max' => 120,
                'name' => 'Kim Tiên',
                'description' => 'Cảnh giới kim tiên, sức mạnh vô song',
            ],
            // Đại La Kim Tiên (Level 121-130)
            [
                'level_min' => 121,
                'level_max' => 130,
                'name' => 'Đại La Kim Tiên',
                'description' => 'Đỉnh cao của kim tiên, một bước đến tiên đế',
            ],
            // Tiên Tôn (Level 141-150)
            [
                'level_min' => 131,
                'level_max' => 140,
                'name' => 'Tiên Tôn',
                'description' => 'Cảnh giới tiên tôn, thống trị một phương',
            ],
            // Tiên Đế (Level 131-140)
            [
                'level_min' => 141,
                'level_max' => 150,
                'name' => 'Tiên Đế',
                'description' => 'Cảnh giới tiên đế, đứng đầu tiên giới ',
            ],
            // Thiên đế (Level 151)
            [
                'level_min' => 151,
                'level_max' => 151,
                'name' => 'Thiên đế',
                'description' => 'Cảnh giới thiên đế, đứng đầu thiên giới ',
            ],
            // Thánh Nhân (Level 152-160)
            [
                'level_min' => 152,
                'level_max' => 160,
                'name' => 'Thánh Nhân',
                'description' => 'Cảnh giới thánh nhân, vượt qua tiên giới',
            ],
            // Thánh Vương (Level 161-170)
            [
                'level_min' => 161,
                'level_max' => 170,
                'name' => 'Thánh Vương',
                'description' => 'Cảnh giới thánh vương, thống trị thánh giới',
            ],
            // Thánh Tôn (Level 171-180)
            [
                'level_min' => 171,
                'level_max' => 180,
                'name' => 'Thánh Tôn',
                'description' => 'Cảnh giới thánh tôn, đỉnh cao của thánh giới',
            ],
            // Đạo Tổ (Level 181-190)
            [
                'level_min' => 181,
                'level_max' => 190,
                'name' => 'Đạo Tổ',
                'description' => 'Cảnh giới đạo tổ, khởi nguồn của đạo',
            ],
            // Vạn Đạo Tổ Sư (Level 191-200)
            [
                'level_min' => 191,
                'level_max' => 200,
                'name' => 'Vạn Đạo Tổ Sư',
                'description' => 'Cảnh giới tối cao, thống trị vạn đạo',
            ],
        ];

        foreach ($levelNames as $levelName) {
            LevelName::create($levelName);
        }
    }
}
