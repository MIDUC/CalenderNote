<?php

namespace App\Services;

use App\Models\Story;
use App\Models\StoryCharacter;
use App\Models\CharacterQuest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoryGeneratorService
{
    /**
     * Generate a new story based on user level and preferences
     */
    public static function generateStoryForUser(User $user, array $options = [])
    {
        $level = $user->level;
        
        // Determine story parameters based on level
        $storyData = self::determineStoryParameters($level, $options);
        
        DB::beginTransaction();
        try {
            // Create story
            $maxOrder = Story::max('order') ?? 0;
            $story = Story::create([
                'title' => $storyData['title'],
                'title_en' => $storyData['title_en'] ?? null,
                'description' => $storyData['description'],
                'source' => $storyData['source'] ?? 'generated',
                'genre' => $storyData['genre'],
                'total_chapters' => $storyData['total_chapters'],
                'unlock_level' => $level,
                'order' => $maxOrder + 1,
                'is_active' => true,
            ]);
            
            // Generate characters for this story
            $characters = self::generateCharactersForStory($story, $level);
            
            // Generate quests for characters
            foreach ($characters as $character) {
                self::generateQuestsForCharacter($character, $level, $user);
            }
            
            DB::commit();
            
            Log::info('Generated story for user', [
                'user_id' => $user->id,
                'story_id' => $story->id,
                'level' => $level,
            ]);
            
            return $story->load('characters.quests');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generating story', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Determine story parameters based on user level
     */
    private static function determineStoryParameters(int $level, array $options = [])
    {
        $genres = ['xianxia', 'wuxia', 'cultivation', 'isekai', 'fantasy', 'martial_arts'];
        $sources = ['china', 'japan', 'korea', 'custom'];
        
        // Select genre and source based on level
        $genre = $options['genre'] ?? $genres[($level - 1) % count($genres)];
        $source = $options['source'] ?? $sources[($level - 1) % count($sources)];
        
        // Generate title and description based on genre
        $templates = self::getStoryTemplates($genre, $source);
        $template = $templates[array_rand($templates)];
        
        return [
            'title' => str_replace('{level}', $level, $template['title']),
            'title_en' => $template['title_en'] ?? null,
            'description' => str_replace('{level}', $level, $template['description']),
            'source' => $source,
            'genre' => $genre,
            'total_chapters' => 100 + ($level * 10), // More chapters for higher levels
        ];
    }

    /**
     * Get story templates based on genre
     */
    private static function getStoryTemplates(string $genre, string $source)
    {
        $templates = [
            'xianxia' => [
                [
                    'title' => 'Tu Tiên Cấp {level}',
                    'title_en' => 'Cultivation Level {level}',
                    'description' => 'Câu chuyện về một tu tiên giả ở cấp độ {level}, từng bước vượt qua các cảnh giới tu luyện.',
                ],
                [
                    'title' => 'Thiên Đạo Cấp {level}',
                    'title_en' => 'Heavenly Dao Level {level}',
                    'description' => 'Hành trình tu luyện theo Thiên Đạo, đạt được sức mạnh siêu nhiên ở cấp độ {level}.',
                ],
            ],
            'wuxia' => [
                [
                    'title' => 'Võ Thánh Cấp {level}',
                    'title_en' => 'Martial Saint Level {level}',
                    'description' => 'Một võ giả ở cấp độ {level}, rèn luyện võ công và đấu tranh với các thế lực hắc ám.',
                ],
            ],
            'isekai' => [
                [
                    'title' => 'Chuyển Sinh Cấp {level}',
                    'title_en' => 'Reincarnation Level {level}',
                    'description' => 'Một người chuyển sinh sang thế giới khác, bắt đầu từ cấp độ {level} và từng bước trở nên mạnh mẽ.',
                ],
            ],
            'fantasy' => [
                [
                    'title' => 'Ma Pháp Sư Cấp {level}',
                    'title_en' => 'Mage Level {level}',
                    'description' => 'Câu chuyện về một ma pháp sư trẻ tuổi ở cấp độ {level}, khám phá thế giới ma pháp.',
                ],
            ],
        ];
        
        return $templates[$genre] ?? $templates['xianxia'];
    }

    /**
     * Generate characters for a story
     */
    private static function generateCharactersForStory(Story $story, int $level)
    {
        $characters = [];
        
        // Always generate a protagonist
        $characters[] = StoryCharacter::create([
            'story_id' => $story->id,
            'name' => self::generateCharacterName($story->source),
            'name_en' => null,
            'description' => 'Nhân vật chính của câu chuyện, bắt đầu từ cấp độ ' . $level,
            'role' => 'protagonist',
            'chapter_appearance' => 1,
            'attributes' => ['level' => $level, 'starting_point' => true],
            'dialogue' => ['Chào mừng đến với thế giới mới!', 'Tôi sẽ trở nên mạnh mẽ!'],
            'order' => 1,
        ]);
        
        // Generate mentor (appears early)
        $characters[] = StoryCharacter::create([
            'story_id' => $story->id,
            'name' => self::generateCharacterName($story->source, 'mentor'),
            'name_en' => null,
            'description' => 'Sư phụ hướng dẫn nhân vật chính, có kinh nghiệm tu luyện',
            'role' => 'mentor',
            'chapter_appearance' => 3,
            'attributes' => ['level' => $level + 5, 'mentor' => true],
            'dialogue' => ['Tu luyện không phải là con đường dễ dàng.', 'Hãy kiên trì và không từ bỏ.'],
            'order' => 2,
        ]);
        
        // Generate ally (appears mid-story)
        $characters[] = StoryCharacter::create([
            'story_id' => $story->id,
            'name' => self::generateCharacterName($story->source, 'ally'),
            'name_en' => null,
            'description' => 'Đồng minh của nhân vật chính, cùng nhau vượt qua khó khăn',
            'role' => 'ally',
            'chapter_appearance' => 10,
            'attributes' => ['level' => $level + 2, 'ally' => true],
            'dialogue' => ['Tôi sẽ luôn ủng hộ bạn!', 'Cùng nhau chiến đấu!'],
            'order' => 3,
        ]);
        
        // Generate antagonist (appears later, stronger)
        $characters[] = StoryCharacter::create([
            'story_id' => $story->id,
            'name' => self::generateCharacterName($story->source, 'antagonist'),
            'name_en' => null,
            'description' => 'Kẻ thù của nhân vật chính, có sức mạnh vượt trội',
            'role' => 'antagonist',
            'chapter_appearance' => 20,
            'attributes' => ['level' => $level + 10, 'enemy' => true],
            'dialogue' => ['Ngươi không thể đánh bại ta!', 'Sức mạnh của ta vượt xa ngươi!'],
            'order' => 4,
        ]);
        
        // Generate rival (appears mid-story)
        if ($level >= 5) {
            $characters[] = StoryCharacter::create([
                'story_id' => $story->id,
                'name' => self::generateCharacterName($story->source, 'rival'),
                'name_en' => null,
                'description' => 'Đối thủ cạnh tranh, luôn muốn vượt qua nhân vật chính',
                'role' => 'rival',
                'chapter_appearance' => 15,
                'attributes' => ['level' => $level + 3, 'rival' => true],
                'dialogue' => ['Ta sẽ chứng minh ta mạnh hơn ngươi!', 'Cuộc thi sẽ quyết định ai mạnh hơn!'],
                'order' => 5,
            ]);
        }
        
        return $characters;
    }

    /**
     * Generate character name based on source
     */
    private static function generateCharacterName(string $source, string $type = 'protagonist')
    {
        $names = [
            'china' => [
                'protagonist' => ['Lâm', 'Vương', 'Trương', 'Lý', 'Triệu', 'Tôn'],
                'mentor' => ['Lão Sư', 'Sư Phụ', 'Đại Sư', 'Tiên Sư'],
                'ally' => ['Lý', 'Vương', 'Trương', 'Triệu'],
                'antagonist' => ['Hắc', 'Ác', 'Tà', 'Ma'],
                'rival' => ['Thiên', 'Vô', 'Cực', 'Thánh'],
            ],
            'japan' => [
                'protagonist' => ['Suzuki', 'Tanaka', 'Yamada', 'Watanabe', 'Sato'],
                'mentor' => ['Master', 'Sensei', 'Shishou'],
                'ally' => ['Yuki', 'Akira', 'Kenji', 'Hiroshi'],
                'antagonist' => ['Dark', 'Shadow', 'Evil'],
                'rival' => ['Ryu', 'Kai', 'Ren'],
            ],
        ];
        
        $sourceNames = $names[$source] ?? $names['china'];
        $typeNames = $sourceNames[$type] ?? $sourceNames['protagonist'];
        
        $first = $typeNames[array_rand($typeNames)];
        $last = ['Phàm', 'Thiên', 'Vô Kỵ', 'Tuyết', 'Vân', 'Hải'][array_rand(['Phàm', 'Thiên', 'Vô Kỵ', 'Tuyết', 'Vân', 'Hải'])];
        
        return $first . ' ' . $last;
    }

    /**
     * Generate quests for a character
     */
    private static function generateQuestsForCharacter(StoryCharacter $character, int $level, User $user)
    {
        $quests = [];
        
        // Generate quests based on character role
        switch ($character->role) {
            case 'protagonist':
                $quests = self::generateProtagonistQuests($character, $level);
                break;
            case 'mentor':
                $quests = self::generateMentorQuests($character, $level);
                break;
            case 'ally':
                $quests = self::generateAllyQuests($character, $level);
                break;
            case 'antagonist':
                $quests = self::generateAntagonistQuests($character, $level);
                break;
            case 'rival':
                $quests = self::generateRivalQuests($character, $level);
                break;
        }
        
        foreach ($quests as $questData) {
            CharacterQuest::create(array_merge($questData, [
                'character_id' => $character->id,
                'level_required' => $level,
            ]));
        }
    }

    /**
     * Generate quests for protagonist
     */
    private static function generateProtagonistQuests(StoryCharacter $character, int $level)
    {
        return [
            [
                'title' => 'Tu luyện cơ bản',
                'description' => 'Hoàn thành ' . (5 + $level) . ' bài tập để tăng cường thể chất',
                'type' => 'exercise',
                'requirements' => ['exercise_count' => 5 + $level],
                'target_count' => 5 + $level,
                'xp_reward' => 50 + ($level * 10),
                'currency_reward' => 25 + ($level * 5),
                'order' => 1,
            ],
            [
                'title' => 'Nhiệm vụ tu luyện',
                'description' => 'Hoàn thành ' . (10 + $level * 2) . ' task để tăng kinh nghiệm',
                'type' => 'task',
                'requirements' => ['task_count' => 10 + ($level * 2)],
                'target_count' => 10 + ($level * 2),
                'xp_reward' => 100 + ($level * 20),
                'currency_reward' => 50 + ($level * 10),
                'order' => 2,
            ],
        ];
    }

    /**
     * Generate quests for mentor
     */
    private static function generateMentorQuests(StoryCharacter $character, int $level)
    {
        return [
            [
                'title' => 'Bài học từ sư phụ',
                'description' => 'Hoàn thành ' . (8 + $level) . ' task để học hỏi từ sư phụ',
                'type' => 'task',
                'requirements' => ['task_count' => 8 + $level],
                'target_count' => 8 + $level,
                'xp_reward' => 80 + ($level * 15),
                'currency_reward' => 40 + ($level * 8),
                'relationship_effects' => ['ally_ids' => [$character->id]],
                'relationship_value_change' => 20,
                'order' => 1,
            ],
        ];
    }

    /**
     * Generate quests for ally
     */
    private static function generateAllyQuests(StoryCharacter $character, int $level)
    {
        return [
            [
                'title' => 'Hợp tác với đồng minh',
                'description' => 'Hoàn thành ' . (6 + $level) . ' task cùng đồng minh',
                'type' => 'task',
                'requirements' => ['task_count' => 6 + $level],
                'target_count' => 6 + $level,
                'xp_reward' => 70 + ($level * 12),
                'currency_reward' => 35 + ($level * 7),
                'relationship_effects' => ['ally_ids' => [$character->id]],
                'relationship_value_change' => 15,
                'order' => 1,
            ],
        ];
    }

    /**
     * Generate quests for antagonist (with heavy penalties)
     */
    private static function generateAntagonistQuests(StoryCharacter $character, int $level)
    {
        return [
            [
                'title' => 'Nhiệm vụ bắt buộc từ kẻ thù',
                'description' => 'Kẻ thù yêu cầu bạn hoàn thành ' . (15 + $level * 2) . ' task trong ' . (3 + floor($level / 5)) . ' ngày. Nếu không hoàn thành, sẽ bị trừ rất nặng!',
                'type' => 'task',
                'requirements' => ['task_count' => 15 + ($level * 2), 'days' => 3 + floor($level / 5)],
                'target_count' => 15 + ($level * 2),
                'xp_reward' => 150 + ($level * 30),
                'currency_reward' => 75 + ($level * 15),
                'relationship_effects' => [],
                'relationship_value_change' => 5, // Small improvement if completed
                'order' => 1,
            ],
        ];
    }

    /**
     * Generate quests for rival
     */
    private static function generateRivalQuests(StoryCharacter $character, int $level)
    {
        return [
            [
                'title' => 'Thách thức đối thủ',
                'description' => 'Hoàn thành ' . (12 + $level * 2) . ' task để chứng minh sức mạnh',
                'type' => 'task',
                'requirements' => ['task_count' => 12 + ($level * 2)],
                'target_count' => 12 + ($level * 2),
                'xp_reward' => 120 + ($level * 25),
                'currency_reward' => 60 + ($level * 12),
                'relationship_effects' => [],
                'relationship_value_change' => 10,
                'order' => 1,
            ],
        ];
    }

    /**
     * Auto-generate story when user levels up
     */
    public static function checkAndGenerateStoryForLevel(User $user)
    {
        $level = $user->level;
        
        // Check if user already has a story for this level
        $existingStory = Story::where('unlock_level', $level)
            ->where('source', 'generated')
            ->first();
        
        if ($existingStory) {
            return null; // Already has story for this level
        }
        
        // Generate new story
        return self::generateStoryForUser($user, [
            'level' => $level,
        ]);
    }
}

