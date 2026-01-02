<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AchievementController extends BaseController
{
    /**
     * Get all achievements with user progress
     */
    public function list(Request $request)
    {
        // Check if achievements table exists
        if (!\Schema::hasTable('achievements')) {
            return $this->sendResponse([], 'Achievements feature is not available.');
        }
        
        $user = $request->user();
        
        $achievements = Achievement::orderBy('order')->get()->map(function($achievement) use ($user) {
            $userAchievement = UserAchievement::where('user_id', $user->id)
                ->where('achievement_id', $achievement->id)
                ->first();
            
            return [
                'id' => $achievement->id,
                'name' => $achievement->name,
                'description' => $achievement->description,
                'icon' => $achievement->icon,
                'type' => $achievement->type,
                'requirements' => $achievement->requirements,
                'xp_reward' => $achievement->xp_reward,
                'currency_reward' => $achievement->currency_reward,
                'rarity' => $achievement->rarity,
                'progress' => $userAchievement->progress ?? 0,
                'is_unlocked' => $userAchievement->is_unlocked ?? false,
                'unlocked_at' => $userAchievement->unlocked_at ?? null,
            ];
        });
        
        return $this->sendResponse($achievements, 'Achievements retrieved successfully.');
    }

    /**
     * Check and update achievement progress
     */
    public static function checkAchievements($user, $type, $data = [])
    {
        // Check if achievements table exists, if not, skip
        if (!Schema::hasTable('achievements')) {
            return;
        }
        
        $achievements = Achievement::where('type', $type)->get();
        
        foreach ($achievements as $achievement) {
            $userAchievement = UserAchievement::firstOrCreate([
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
            ]);
            
            if ($userAchievement->is_unlocked) {
                continue; // Already unlocked
            }
            
            $progress = self::calculateProgress($user, $achievement, $data);
            $userAchievement->progress = $progress;
            
            // Check if requirements are met
            if (self::checkRequirements($user, $achievement, $progress)) {
                $userAchievement->is_unlocked = true;
                $userAchievement->unlocked_at = now();
                $userAchievement->save();
                
                // Give rewards
                if ($achievement->xp_reward > 0) {
                    GamificationService::addXP($user, $achievement->xp_reward);
                }
                if ($achievement->currency_reward > 0) {
                    GamificationService::addCurrency($user, $achievement->currency_reward);
                }
            } else {
                $userAchievement->save();
            }
        }
    }

    /**
     * Calculate progress for an achievement
     */
    private static function calculateProgress($user, $achievement, $data)
    {
        $requirements = $achievement->requirements ?? [];
        
        switch ($achievement->type) {
            case 'task':
                return $user->tasks()->where('status', 'done')->count();
            case 'level':
                return $user->level;
            case 'currency':
                return $user->currency;
            case 'quest':
                // Check if quests relationship exists
                if (\Illuminate\Support\Facades\Schema::hasTable('user_quests') && \Illuminate\Support\Facades\Schema::hasTable('quests')) {
                    try {
                        return $user->quests()->where('status', 'claimed')->count();
                    } catch (\Exception $e) {
                        return 0;
                    }
                }
                return 0;
            case 'battle':
                // Check if battles table exists
                if (\Illuminate\Support\Facades\Schema::hasTable('battles')) {
                    try {
                        return $user->battles()->where('status', 'won')->count();
                    } catch (\Exception $e) {
                        return 0;
                    }
                }
                return 0;
            case 'streak':
                return $user->current_streak;
            default:
                return 0;
        }
    }

    /**
     * Check if achievement requirements are met
     */
    private static function checkRequirements($user, $achievement, $progress)
    {
        $requirements = $achievement->requirements ?? [];
        
        if (isset($requirements['task_count'])) {
            return $progress >= $requirements['task_count'];
        }
        if (isset($requirements['level'])) {
            return $progress >= $requirements['level'];
        }
        if (isset($requirements['currency'])) {
            return $progress >= $requirements['currency'];
        }
        if (isset($requirements['quest_count'])) {
            return $progress >= $requirements['quest_count'];
        }
        if (isset($requirements['battle_count'])) {
            return $progress >= $requirements['battle_count'];
        }
        if (isset($requirements['streak'])) {
            return $progress >= $requirements['streak'];
        }
        
        return false;
    }
}
