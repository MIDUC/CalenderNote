<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Story;

class StorySeeder extends Seeder
{
    public function run(): void
    {
        $stories = [
            // Trung Quốc - Tiên Hiệp
            [
                'title' => 'Tu Tiên Truyền Kỳ',
                'title_en' => 'Immortal Cultivation Legend',
                'description' => 'Câu chuyện về một thiếu niên bình thường bước vào con đường tu tiên, từng bước vượt qua các cảnh giới, đạt được sức mạnh siêu nhiên.',
                'source' => 'china',
                'genre' => 'xianxia',
                'total_chapters' => 1000,
                'unlock_level' => 1,
                'order' => 1,
            ],
            [
                'title' => 'Võ Thánh Truyền Kỳ',
                'title_en' => 'Martial Saint Legend',
                'description' => 'Hành trình của một võ giả từ thấp đến cao, rèn luyện võ công, đấu tranh với các thế lực hắc ám.',
                'source' => 'china',
                'genre' => 'wuxia',
                'total_chapters' => 800,
                'unlock_level' => 5,
                'order' => 2,
            ],
            [
                'title' => 'Thiên Đạo Tu Luyện',
                'title_en' => 'Heavenly Dao Cultivation',
                'description' => 'Tu luyện theo Thiên Đạo, khám phá bí mật của vũ trụ, đạt được sức mạnh tối thượng.',
                'source' => 'china',
                'genre' => 'cultivation',
                'total_chapters' => 1200,
                'unlock_level' => 10,
                'order' => 3,
            ],
            // Nhật Bản - Isekai
            [
                'title' => 'Chuyển Sinh Thế Giới Khác',
                'title_en' => 'Reincarnated in Another World',
                'description' => 'Một học sinh bình thường chuyển sinh sang thế giới ma pháp, sử dụng kiến thức hiện đại để trở nên mạnh mẽ.',
                'source' => 'japan',
                'genre' => 'isekai',
                'total_chapters' => 500,
                'unlock_level' => 1,
                'order' => 4,
            ],
            [
                'title' => 'Ma Pháp Sư Vô Địch',
                'title_en' => 'Invincible Mage',
                'description' => 'Câu chuyện về một ma pháp sư trẻ tuổi với tài năng thiên bẩm, từng bước trở thành ma pháp sư mạnh nhất.',
                'source' => 'japan',
                'genre' => 'fantasy',
                'total_chapters' => 600,
                'unlock_level' => 3,
                'order' => 5,
            ],
            [
                'title' => 'Kiếm Thánh Đường',
                'title_en' => 'Sword Saint Path',
                'description' => 'Hành trình của một kiếm sĩ trẻ, rèn luyện kiếm thuật đến cảnh giới tối cao, đấu tranh với các kiếm thánh khác.',
                'source' => 'japan',
                'genre' => 'martial_arts',
                'total_chapters' => 700,
                'unlock_level' => 7,
                'order' => 6,
            ],
            // Hàn Quốc
            [
                'title' => 'Thần Cấp Thợ Săn',
                'title_en' => 'S-Class Hunter',
                'description' => 'Trong thế giới nơi các cổng không gian xuất hiện, một thợ săn trẻ tuổi từng bước trở thành thợ săn cấp S.',
                'source' => 'korea',
                'genre' => 'dungeon',
                'total_chapters' => 400,
                'unlock_level' => 2,
                'order' => 7,
            ],
        ];

        foreach ($stories as $story) {
            Story::firstOrCreate(
                ['title' => $story['title']],
                $story
            );
        }
    }
}

