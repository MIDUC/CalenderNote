<?php

namespace App\Services;

use App\Models\StoryRule;
use App\Models\UserStoryRuleProgress;
use App\Models\UserCharacterRelationship;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoryRuleService
{
    /**
     * Check and process rules when a trigger event occurs
     */
    public static function checkRules(User $user, string $triggerType, array $triggerData = [])
    {
        // Get active rules for this trigger type
        $rules = StoryRule::where('trigger_type', $triggerType)
            ->where('is_active', true)
            ->get();
        
        foreach ($rules as $rule) {
            // Check if rule applies to this user/story
            if ($rule->story_id && !self::userHasStory($user, $rule->story_id)) {
                continue;
            }
            
            // Check trigger conditions
            if (!self::checkTriggerConditions($rule, $triggerData)) {
                continue;
            }
            
            // Process the rule
            self::processRule($user, $rule, $triggerData);
        }
    }

    /**
     * Check if trigger conditions are met
     */
    private static function checkTriggerConditions(StoryRule $rule, array $triggerData): bool
    {
        if (!$rule->trigger_conditions) {
            return true;
        }
        
        $conditions = $rule->trigger_conditions;
        
        // Check monster_id condition
        if (isset($conditions['monster_id'])) {
            if (!isset($triggerData['monster_id']) || $triggerData['monster_id'] != $conditions['monster_id']) {
                return false;
            }
        }
        
        // Check task_type condition
        if (isset($conditions['task_type'])) {
            if (!isset($triggerData['task_type']) || $triggerData['task_type'] != $conditions['task_type']) {
                return false;
            }
        }
        
        // Add more condition checks as needed
        
        return true;
    }

    /**
     * Process a rule for a user
     */
    private static function processRule(User $user, StoryRule $rule, array $triggerData)
    {
        DB::beginTransaction();
        try {
            // Get or create progress
            $progress = UserStoryRuleProgress::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'story_rule_id' => $rule->id,
                ],
                [
                    'current_count' => 0,
                    'relationship_value' => 0,
                    'penalty_applied' => false,
                ]
            );
            
            // Increment count
            if ($rule->is_cumulative) {
                $progress->current_count += 1;
            } else {
                $progress->current_count = 1;
            }
            
            $progress->last_triggered_at = now();
            $progress->save();
            
            // Apply relationship change each time (if cumulative) or only when count reached
            if ($rule->target_character_id && $rule->relationship_value_change != 0) {
                if ($rule->is_cumulative) {
                    // Apply change each time
                    self::applyRelationshipChange($user, $rule, $progress);
                } else {
                    // Only apply when required count reached
                    if ($progress->current_count >= $rule->required_count) {
                        self::applyRelationshipChange($user, $rule, $progress);
                    }
                }
            }
            
            // Check if required count is reached for penalty check
            if ($progress->current_count >= $rule->required_count) {
                // Check if threshold reached and apply penalty
                if ($rule->has_penalty && $rule->relationship_threshold) {
                    self::checkAndApplyPenalty($user, $rule, $progress);
                }
                
                // Reset if needed
                if ($rule->reset_on_complete) {
                    $progress->current_count = 0;
                    $progress->save();
                }
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing story rule', [
                'user_id' => $user->id,
                'rule_id' => $rule->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Apply relationship change
     */
    private static function applyRelationshipChange(User $user, StoryRule $rule, UserStoryRuleProgress $progress)
    {
        $relationship = UserCharacterRelationship::firstOrNew([
            'user_id' => $user->id,
            'character_id' => $rule->target_character_id,
        ]);
        
        // Calculate new relationship value
        $newValue = $relationship->relationship_value + $rule->relationship_value_change;
        $newValue = max(-100, min(100, $newValue));
        
        $relationship->relationship_value = $newValue;
        
        // Update relationship type if threshold reached
        if ($rule->relationship_type_change && abs($newValue) >= abs($rule->relationship_threshold)) {
            $relationship->relationship_type = $rule->relationship_type_change;
        } else {
            // Auto-update type based on value
            if ($newValue <= -50) {
                $relationship->relationship_type = 'enemy';
            } elseif ($newValue >= 50) {
                $relationship->relationship_type = 'ally';
            } else {
                $relationship->relationship_type = 'neutral';
            }
        }
        
        $relationship->save();
        
        // Update progress relationship value
        $progress->relationship_value = $newValue;
        $progress->save();
        
        // Set penalty due date if threshold reached
        if ($rule->has_penalty && abs($newValue) >= abs($rule->relationship_threshold)) {
            if (!$progress->penalty_due_at && $rule->penalty_due_days) {
                $progress->penalty_due_at = now()->addDays($rule->penalty_due_days);
                $progress->save();
            }
        }
    }

    /**
     * Check and apply penalty if threshold reached and due date passed
     */
    private static function checkAndApplyPenalty(User $user, StoryRule $rule, UserStoryRuleProgress $progress)
    {
        // Check if threshold reached
        if (abs($progress->relationship_value) < abs($rule->relationship_threshold)) {
            return;
        }
        
        // Check if penalty already applied
        if ($progress->penalty_applied) {
            return;
        }
        
        // Check if due date passed
        if ($rule->penalty_due_days && $progress->penalty_due_at) {
            if (now()->lt($progress->penalty_due_at)) {
                return; // Not due yet
            }
        }
        
        // Apply penalty
        if ($rule->penalty_effects) {
            $effects = $rule->penalty_effects;
            
            // Level down
            if (isset($effects['level_down']) && $effects['level_down'] > 0) {
                for ($i = 0; $i < $effects['level_down']; $i++) {
                    if ($user->level > 1) {
                        GamificationService::levelDown($user);
                    }
                }
            }
            
            // XP loss
            if (isset($effects['xp_loss']) && $effects['xp_loss'] > 0) {
                GamificationService::subtractXP($user, $effects['xp_loss']);
            }
            
            // Currency loss
            if (isset($effects['currency_loss']) && $effects['currency_loss'] > 0) {
                GamificationService::subtractCurrency($user, $effects['currency_loss']);
            }
        }
        
        $progress->penalty_applied = true;
        $progress->save();
        
        Log::info('Penalty applied to user', [
            'user_id' => $user->id,
            'rule_id' => $rule->id,
            'penalty_effects' => $rule->penalty_effects,
        ]);
    }

    /**
     * Check if user has access to a story
     */
    private static function userHasStory(User $user, int $storyId): bool
    {
        // Check if user's level is high enough for story
        $story = \App\Models\Story::find($storyId);
        if (!$story) {
            return false;
        }
        
        return $user->level >= $story->unlock_level;
    }

    /**
     * Process overdue penalties (should be called by scheduled command)
     */
    public static function processOverduePenalties()
    {
        $overdueProgresses = UserStoryRuleProgress::whereNotNull('penalty_due_at')
            ->where('penalty_due_at', '<=', now())
            ->where('penalty_applied', false)
            ->with(['user', 'storyRule'])
            ->get();
        
        foreach ($overdueProgresses as $progress) {
            if ($progress->storyRule && $progress->storyRule->has_penalty) {
                self::checkAndApplyPenalty($progress->user, $progress->storyRule, $progress);
            }
        }
    }
}

