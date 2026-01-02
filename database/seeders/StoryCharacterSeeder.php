<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Story;
use App\Models\StoryCharacter;

class StoryCharacterSeeder extends Seeder
{
    public function run(): void
    {
        // Tu Tiên Truyền Kỳ
        $story1 = Story::where('title', 'Tu Tiên Truyền Kỳ')->first();
        if ($story1) {
            $characters = [
                [
                    'name' => 'Lâm Phàm',
                    'name_en' => 'Lin Fan',
                    'description' => 'Nhân vật chính, một thiếu niên bình thường bước vào con đường tu tiên',
                    'role' => 'protagonist',
                    'chapter_appearance' => 1,
                    'attributes' => ['cultivation_level' => 'Qi Refining', 'element' => 'Fire', 'talent' => 'Heavenly'],
                    'dialogue' => ['Chào mừng đến với thế giới tu tiên!', 'Tôi sẽ trở thành cường giả mạnh nhất!'],
                    'order' => 1,
                ],
                [
                    'name' => 'Lão Sư Vân',
                    'name_en' => 'Master Yun',
                    'description' => 'Sư phụ của Lâm Phàm, một cao thủ tu tiên',
                    'role' => 'mentor',
                    'chapter_appearance' => 5,
                    'attributes' => ['cultivation_level' => 'Nascent Soul', 'element' => 'Wind'],
                    'dialogue' => ['Tu tiên không phải là con đường dễ dàng.', 'Hãy nhớ rằng kiên trì là chìa khóa.'],
                    'order' => 2,
                ],
                [
                    'name' => 'Vương Thiên',
                    'name_en' => 'Wang Tian',
                    'description' => 'Thiếu chủ tông môn, kẻ thù của Lâm Phàm',
                    'role' => 'antagonist',
                    'chapter_appearance' => 20,
                    'attributes' => ['cultivation_level' => 'Foundation Building', 'element' => 'Thunder', 'faction' => 'Thunder Sect'],
                    'dialogue' => ['Ngươi dám đối đầu với ta?', 'Ta sẽ nghiền nát ngươi!'],
                    'order' => 3,
                ],
                [
                    'name' => 'Lý Tuyết',
                    'name_en' => 'Li Xue',
                    'description' => 'Đệ tử tông môn, bạn thân của Lâm Phàm',
                    'role' => 'ally',
                    'chapter_appearance' => 10,
                    'attributes' => ['cultivation_level' => 'Qi Refining', 'element' => 'Ice'],
                    'dialogue' => ['Lâm Phàm, ta sẽ luôn ủng hộ ngươi!', 'Cùng nhau vượt qua khó khăn!'],
                    'order' => 4,
                ],
                [
                    'name' => 'Trương Vô Kỵ',
                    'name_en' => 'Zhang Wuji',
                    'description' => 'Đại sư huynh, đối thủ cạnh tranh',
                    'role' => 'rival',
                    'chapter_appearance' => 15,
                    'attributes' => ['cultivation_level' => 'Foundation Building', 'element' => 'Metal'],
                    'dialogue' => ['Ta sẽ chứng minh ta mạnh hơn ngươi!', 'Cuộc thi tu tiên sẽ quyết định ai mạnh hơn!'],
                    'order' => 5,
                ],
            ];
            foreach ($characters as $char) {
                StoryCharacter::firstOrCreate(
                    ['story_id' => $story1->id, 'name' => $char['name']],
                    array_merge($char, ['story_id' => $story1->id])
                );
            }
        }

        // Chuyển Sinh Thế Giới Khác
        $story2 = Story::where('title', 'Chuyển Sinh Thế Giới Khác')->first();
        if ($story2) {
            $characters = [
                [
                    'name' => 'Suzuki Taro',
                    'name_en' => 'Suzuki Taro',
                    'description' => 'Nhân vật chính, một học sinh chuyển sinh sang thế giới ma pháp',
                    'role' => 'protagonist',
                    'chapter_appearance' => 1,
                    'attributes' => ['magic_affinity' => 'All Elements', 'level' => 1],
                    'dialogue' => ['Tôi đã chuyển sinh?', 'Thế giới này thật thú vị!'],
                    'order' => 1,
                ],
                [
                    'name' => 'Elise',
                    'name_en' => 'Elise',
                    'description' => 'Công chúa ma pháp, người yêu của nhân vật chính',
                    'role' => 'love_interest',
                    'chapter_appearance' => 10,
                    'attributes' => ['magic_affinity' => 'Ice', 'level' => 50],
                    'dialogue' => ['Bạn thật mạnh mẽ!', 'Hãy cùng nhau khám phá thế giới này.'],
                    'order' => 2,
                ],
                [
                    'name' => 'Duke Valdris',
                    'name_en' => 'Duke Valdris',
                    'description' => 'Quý tộc ma pháp, kẻ thù của Suzuki',
                    'role' => 'antagonist',
                    'chapter_appearance' => 25,
                    'attributes' => ['magic_affinity' => 'Dark', 'level' => 80, 'faction' => 'Dark Nobles'],
                    'dialogue' => ['Kẻ thường dân như ngươi không xứng với công chúa!', 'Ta sẽ tiêu diệt ngươi!'],
                    'order' => 3,
                ],
                [
                    'name' => 'Marcus',
                    'name_en' => 'Marcus',
                    'description' => 'Hiệp sĩ ma pháp, đồng minh của Suzuki',
                    'role' => 'ally',
                    'chapter_appearance' => 15,
                    'attributes' => ['magic_affinity' => 'Light', 'level' => 60],
                    'dialogue' => ['Tôi sẽ bảo vệ bạn!', 'Cùng nhau chiến đấu!'],
                    'order' => 4,
                ],
                [
                    'name' => 'Luna',
                    'name_en' => 'Luna',
                    'description' => 'Pháp sư bí ẩn, có thể là bạn hoặc thù',
                    'role' => 'supporting',
                    'chapter_appearance' => 30,
                    'attributes' => ['magic_affinity' => 'Space', 'level' => 70],
                    'dialogue' => ['Vận mệnh của ngươi rất thú vị...', 'Hãy cẩn thận với những lựa chọn của mình.'],
                    'order' => 5,
                ],
            ];
            foreach ($characters as $char) {
                StoryCharacter::firstOrCreate(
                    ['story_id' => $story2->id, 'name' => $char['name']],
                    array_merge($char, ['story_id' => $story2->id])
                );
            }
        }

        // Võ Thánh Truyền Kỳ
        $story3 = Story::where('title', 'Võ Thánh Truyền Kỳ')->first();
        if ($story3) {
            $characters = [
                [
                    'name' => 'Võ Thánh',
                    'name_en' => 'Martial Saint',
                    'description' => 'Nhân vật chính, một võ giả trẻ tuổi',
                    'role' => 'protagonist',
                    'chapter_appearance' => 1,
                    'attributes' => ['martial_level' => 'Beginner', 'style' => 'Internal'],
                    'dialogue' => ['Võ đạo là con đường của ta!', 'Ta sẽ trở thành võ thánh!'],
                    'order' => 1,
                ],
                [
                    'name' => 'Sư Phụ Lão',
                    'name_en' => 'Old Master',
                    'description' => 'Sư phụ võ thuật',
                    'role' => 'mentor',
                    'chapter_appearance' => 3,
                    'attributes' => ['martial_level' => 'Grandmaster', 'style' => 'Internal'],
                    'dialogue' => ['Võ đạo không chỉ là sức mạnh, mà còn là tâm hồn.', 'Hãy tu luyện từ tâm.'],
                    'order' => 2,
                ],
                [
                    'name' => 'Hắc Võ Thần',
                    'name_en' => 'Dark Martial God',
                    'description' => 'Kẻ thù lớn nhất, võ thánh hắc ám',
                    'role' => 'villain',
                    'chapter_appearance' => 50,
                    'attributes' => ['martial_level' => 'Supreme', 'style' => 'Dark'],
                    'dialogue' => ['Ngươi không thể đánh bại ta!', 'Sức mạnh hắc ám sẽ thống trị!'],
                    'order' => 3,
                ],
            ];
            foreach ($characters as $char) {
                StoryCharacter::firstOrCreate(
                    ['story_id' => $story3->id, 'name' => $char['name']],
                    array_merge($char, ['story_id' => $story3->id])
                );
            }
        }
    }
}
