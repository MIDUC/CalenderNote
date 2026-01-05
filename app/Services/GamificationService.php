<?php

namespace App\Services;

use App\Models\User;
use App\Models\LevelName;
use App\Models\LevelXpRequirement;
use Illuminate\Support\Facades\Log;

class GamificationService
{
    /**
     * Calculate XP required for a specific level (from level-1 to level)
     * Uses database configuration if available, otherwise falls back to formula
     */
    public static function getXPForLevel(int $level): int
    {
        return LevelXpRequirement::getXPForLevel($level);
    }

    /**
     * Calculate total XP needed to reach a level (sum of all previous levels)
     * This is used for display purposes only
     */
    public static function getTotalXPForLevel(int $level): int
    {
        return LevelXpRequirement::getTotalXPForLevel($level);
    }

    /**
     * Add XP to user without leveling up (for manual breakthrough)
     * Just adds XP, doesn't check for level up
     */
    public static function addXPWithoutLevelUp(User $user, int $xp): void
    {
        $user->exp += $xp;
        $user->save();
    }

    /**
     * Check if user can breakthrough (has enough XP for next level)
     */
    public static function canBreakthrough(User $user): bool
    {
        $xpNeededForNext = self::getTotalXPForLevel($user->level + 1) - self::getTotalXPForLevel($user->level);
        return $user->exp >= $xpNeededForNext;
    }

    /**
     * Perform breakthrough - level up manually
     * Returns level up result
     */
    public static function breakthrough(User $user): array
    {
        $oldLevel = $user->level;
        $xpNeededForNext = self::getTotalXPForLevel($user->level + 1) - self::getTotalXPForLevel($user->level);
        
        if ($user->exp < $xpNeededForNext) {
            return [
                'success' => false,
                'message' => 'Not enough XP for breakthrough',
                'current_exp' => $user->exp,
                'required_exp' => $xpNeededForNext,
            ];
        }

        // Level up!
        $user->exp -= $xpNeededForNext; // Subtract XP required for this level
        $user->level++;
        
        // Update level name
        $levelName = LevelName::getNameForLevel($user->level);
        if ($levelName) {
            $user->level_name = $levelName;
        }
        
        $user->save();

        // Auto-generate story when leveling up
        $generatedStory = null;
        try {
            $generatedStory = \App\Services\StoryGeneratorService::checkAndGenerateStoryForLevel($user);
        } catch (\Exception $e) {
            Log::error('Error auto-generating story on breakthrough', [
                'user_id' => $user->id,
                'level' => $user->level,
                'error' => $e->getMessage(),
            ]);
        }

        return [
            'success' => true,
            'leveled_up' => true,
            'old_level' => $oldLevel,
            'new_level' => $user->level,
            'current_exp' => $user->exp,
            'exp_for_next_level' => self::getTotalXPForLevel($user->level + 1) - self::getTotalXPForLevel($user->level),
            'generated_story' => $generatedStory,
        ];
    }

    /**
     * Add XP to user and check for level up
     * When leveling up, subtract the XP required for that level
     */
    public static function addXP(User $user, int $xp): array
    {
        $oldLevel = $user->level;
        $user->exp += $xp;
        
        // Check for level up - subtract XP when leveling up
        $leveledUp = false;
        $levelsGained = 0;
        
        while (true) {
            $xpNeededForNext = self::getTotalXPForLevel($user->level + 1) - self::getTotalXPForLevel($user->level);
            
            if ($user->exp >= $xpNeededForNext) {
                // Level up!
                $user->exp -= $xpNeededForNext; // Subtract XP required for this level
                $user->level++;
                $leveledUp = true;
                $levelsGained++;
                
                // Update level name with tier
                $levelName = LevelName::getNameWithTierForLevel($user->level);
                if ($levelName) {
                    $user->level_name = $levelName;
                }
            } else {
                // Not enough XP for next level
                break;
            }
        }
        
        $user->save();
        
        $xpNeededForNextLevel = self::getTotalXPForLevel($user->level + 1) - self::getTotalXPForLevel($user->level);
        
        // Auto-generate story when leveling up
        $generatedStory = null;
        if ($leveledUp) {
            try {
                $generatedStory = \App\Services\StoryGeneratorService::checkAndGenerateStoryForLevel($user);
            } catch (\Exception $e) {
                Log::error('Error auto-generating story on level up', [
                    'user_id' => $user->id,
                    'level' => $user->level,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        return [
            'leveled_up' => $leveledUp,
            'levels_gained' => $levelsGained,
            'old_level' => $oldLevel,
            'new_level' => $user->level,
            'current_exp' => $user->exp,
            'exp_for_next_level' => $xpNeededForNextLevel,
            'generated_story' => $generatedStory,
        ];
    }

    /**
     * Add currency (linh thạch) to user
     */
    public static function addCurrency(User $user, int $amount): void
    {
        $user->currency += $amount;
        $user->save();
    }

    /**
     * Reward user for completing a task
     */
    public static function rewardTaskCompletion(User $user, array $taskData = []): array
    {
        // Base rewards
        $baseXP = 10;
        $baseCurrency = 5;
        
        // Bonus based on task difficulty or other factors
        $xpBonus = $taskData['xp_bonus'] ?? 0;
        $currencyBonus = $taskData['currency_bonus'] ?? 0;
        
        $totalXP = $baseXP + $xpBonus;
        $totalCurrency = $baseCurrency + $currencyBonus;
        
        // Add rewards
        $levelResult = self::addXP($user, $totalXP);
        self::addCurrency($user, $totalCurrency);
        
        return [
            'xp_gained' => $totalXP,
            'currency_gained' => $totalCurrency,
            'level_result' => $levelResult,
        ];
    }

    /**
     * Get progress percentage to next level
     */
    public static function getProgressToNextLevel(User $user): float
    {
        $xpNeededForNext = self::getTotalXPForLevel($user->level + 1) - self::getTotalXPForLevel($user->level);
        
        if ($xpNeededForNext <= 0) {
            return 100.0;
        }
        
        return min(100.0, ($user->exp / $xpNeededForNext) * 100);
    }
    
    /**
     * Get XP needed for current level (from level start to next level)
     */
    public static function getXPNeededForCurrentLevel(int $level): int
    {
        return self::getTotalXPForLevel($level + 1) - self::getTotalXPForLevel($level);
    }

    /**
     * Update streak when user completes a task
     * Returns streak bonus information
     */
    public static function updateStreak(User $user): array
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        
        $streakBonus = 0;
        $currencyBonus = 0;
        
        if (!$user->last_task_date) {
            // First task ever
            $user->current_streak = 1;
            $user->last_task_date = $today;
        } elseif ($user->last_task_date == $today) {
            // Already completed task today, no change
            // But still give streak bonus
        } elseif ($user->last_task_date == $yesterday) {
            // Continuing streak
            $user->current_streak++;
            $user->last_task_date = $today;
        } else {
            // Streak broken, reset to 1
            $user->current_streak = 1;
            $user->last_task_date = $today;
        }
        
        // Update longest streak
        if ($user->current_streak > $user->longest_streak) {
            $user->longest_streak = $user->current_streak;
        }
        
        // Calculate streak bonus (increases with streak length)
        if ($user->current_streak > 1) {
            // Bonus: 1% XP per day of streak, max 50%
            $streakBonusPercent = min(50, $user->current_streak);
            $streakBonus = (int) (10 * ($streakBonusPercent / 100)); // Base 10 XP
            
            // Currency bonus: 1 linh thạch per day of streak, max 50
            $currencyBonus = min(50, $user->current_streak);
        }
        
        $user->save();
        
        return [
            'current_streak' => $user->current_streak,
            'longest_streak' => $user->longest_streak,
            'streak_bonus_xp' => $streakBonus,
            'streak_bonus_currency' => $currencyBonus,
        ];
    }

    /**
     * Get streak milestone rewards (bonus at 7, 30, 100 days)
     */
    public static function getStreakMilestoneReward(int $streak): ?array
    {
        $milestones = [
            7 => ['xp' => 50, 'currency' => 25],
            30 => ['xp' => 200, 'currency' => 100],
            100 => ['xp' => 1000, 'currency' => 500],
        ];
        
        return $milestones[$streak] ?? null;
    }

    /**
     * Subtract XP from user safely (won't go below 0, won't level down)
     * Returns the actual amount subtracted
     */
    public static function subtractXP(User $user, int $xp): array
    {
        $oldExp = $user->exp;
        $oldLevel = $user->level;
        
        // Calculate minimum XP for current level (can't go below this)
        $minXPForCurrentLevel = 0; // XP starts at 0 for each level
        
        // Subtract XP, but don't go below 0
        $user->exp = max($minXPForCurrentLevel, $user->exp - $xp);
        
        // Ensure level doesn't decrease
        // If XP goes negative, we keep it at 0 but don't level down
        if ($user->exp < 0) {
            $user->exp = 0;
        }
        
        $actualSubtracted = $oldExp - $user->exp;
        
        $user->save();
        
        return [
            'xp_subtracted' => $actualSubtracted,
            'requested_xp' => $xp,
            'old_exp' => $oldExp,
            'new_exp' => $user->exp,
            'old_level' => $oldLevel,
            'new_level' => $user->level, // Should be same as old_level
            'level_changed' => false, // Never level down
        ];
    }

    /**
     * Level down user (subtract 1 level, reset XP to 0)
     */
    public static function levelDown(User $user): void
    {
        if ($user->level > 1) {
            $user->level--;
            $user->exp = 0; // Reset XP when leveling down
            $user->save();
            
            Log::info('User leveled down', [
                'user_id' => $user->id,
                'new_level' => $user->level,
            ]);
        }
    }

    /**
     * Subtract currency safely
     */
    public static function subtractCurrency(User $user, int $amount): void
    {
        $user->currency = max(0, $user->currency - $amount);
        $user->save();
    }
}

