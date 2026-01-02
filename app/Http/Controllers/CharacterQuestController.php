<?php

namespace App\Http\Controllers;

use App\Models\CharacterQuest;
use App\Models\UserCharacterQuest;
use App\Models\UserCharacterRelationship;
use App\Services\GamificationService;
use App\Models\UserItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CharacterQuestController extends BaseController
{
    /**
     * Get available quests for a character
     */
    public function listByCharacter(Request $request, int $characterId)
    {
        $user = $request->user();
        $quests = CharacterQuest::where('character_id', $characterId)
            ->where('is_active', true)
            ->where('level_required', '<=', $user->level)
            ->with('character', 'itemReward')
            ->orderBy('order')
            ->get()
            ->map(function($quest) use ($user) {
                // Check if user already has this quest
                $userQuest = UserCharacterQuest::where('user_id', $user->id)
                    ->where('character_quest_id', $quest->id)
                    ->first();
                
                $quest->user_status = $userQuest ? $userQuest->status : 'available';
                $quest->user_progress = $userQuest ? $userQuest->progress : 0;
                $quest->due_date = $userQuest ? $userQuest->due_date : null;
                
                return $quest;
            });
        
        return $this->sendResponse($quests, 'Character quests retrieved successfully.');
    }

    /**
     * Accept a character quest
     */
    public function accept(Request $request, int $questId)
    {
        $user = $request->user();
        
        DB::beginTransaction();
        try {
            $quest = CharacterQuest::with('character')->findOrFail($questId);
            
            // Check level requirement
            if ($user->level < $quest->level_required) {
                return $this->sendError('Cấp độ không đủ để nhận nhiệm vụ này.', [], 400);
            }
            
            // Check if already accepted
            $existingQuest = UserCharacterQuest::where('user_id', $user->id)
                ->where('character_quest_id', $questId)
                ->whereIn('status', ['in_progress', 'available'])
                ->first();
            
            if ($existingQuest) {
                return $this->sendError('Bạn đã nhận nhiệm vụ này rồi.', [], 400);
            }
            
            // Calculate due date based on requirements
            $dueDate = null;
            if (isset($quest->requirements['days'])) {
                $dueDate = Carbon::today()->addDays($quest->requirements['days']);
            }
            
            // Create user quest
            $userQuest = UserCharacterQuest::create([
                'user_id' => $user->id,
                'character_quest_id' => $questId,
                'status' => 'in_progress',
                'progress' => 0,
                'accepted_at' => Carbon::today(),
                'due_date' => $dueDate,
                'xp_penalty' => $this->calculatePenalty($quest, $user),
                'currency_penalty' => $this->calculateCurrencyPenalty($quest, $user),
            ]);
            
            // Apply relationship effects
            $this->applyRelationshipEffects($user, $quest);
            
            DB::commit();
            
            return $this->sendResponse([
                'quest' => $quest->load('character', 'itemReward'),
                'user_quest' => $userQuest,
                'message' => 'Đã nhận nhiệm vụ thành công!',
            ], 'Quest accepted successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error accepting character quest', [
                'quest_id' => $questId,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            
            return $this->sendError('Không thể nhận nhiệm vụ. Vui lòng thử lại.', [], 500);
        }
    }

    /**
     * Complete a character quest
     */
    public function complete(Request $request, int $userQuestId)
    {
        $user = $request->user();
        
        DB::beginTransaction();
        try {
            $userQuest = UserCharacterQuest::where('user_id', $user->id)
                ->with('characterQuest.character')
                ->findOrFail($userQuestId);
            
            if ($userQuest->status !== 'in_progress') {
                return $this->sendError('Nhiệm vụ này không thể hoàn thành.', [], 400);
            }
            
            $quest = $userQuest->characterQuest;
            
            // Check if requirements are met
            if (!$this->checkRequirements($user, $quest)) {
                return $this->sendError('Chưa đủ điều kiện để hoàn thành nhiệm vụ.', [], 400);
            }
            
            // Mark as completed
            $userQuest->status = 'completed';
            $userQuest->completed_at = now();
            $userQuest->save();
            
            // Give rewards
            $rewards = [];
            if ($quest->xp_reward > 0) {
                GamificationService::addXP($user, $quest->xp_reward);
                $rewards['xp'] = $quest->xp_reward;
            }
            if ($quest->currency_reward > 0) {
                GamificationService::addCurrency($user, $quest->currency_reward);
                $rewards['currency'] = $quest->currency_reward;
            }
            if ($quest->item_reward_id && $quest->item_reward_quantity > 0) {
                $userItem = UserItem::firstOrNew([
                    'user_id' => $user->id,
                    'item_id' => $quest->item_reward_id,
                ]);
                $userItem->quantity += $quest->item_reward_quantity;
                $userItem->save();
                $rewards['item'] = $quest->itemReward->name;
                $rewards['item_quantity'] = $quest->item_reward_quantity;
            }
            
            // Update relationship
            if ($quest->relationship_value_change != 0) {
                $this->updateRelationship($user, $quest->character_id, $quest->relationship_value_change);
            }
            
            DB::commit();
            
            return $this->sendResponse([
                'rewards' => $rewards,
                'message' => 'Hoàn thành nhiệm vụ thành công!',
            ], 'Quest completed successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing character quest', [
                'user_quest_id' => $userQuestId,
                'error' => $e->getMessage(),
            ]);
            
            return $this->sendError('Không thể hoàn thành nhiệm vụ. Vui lòng thử lại.', [], 500);
        }
    }

    /**
     * Get user's active quests
     */
    public function myQuests(Request $request)
    {
        $user = $request->user();
        $quests = UserCharacterQuest::where('user_id', $user->id)
            ->whereIn('status', ['in_progress', 'available'])
            ->with('characterQuest.character.story', 'characterQuest.itemReward')
            ->orderBy('due_date', 'asc')
            ->get();
        
        return $this->sendResponse($quests, 'User quests retrieved successfully.');
    }

    /**
     * Apply relationship effects when accepting a quest
     */
    private function applyRelationshipEffects($user, $quest)
    {
        if (!$quest->relationship_effects) {
            return;
        }
        
        $effects = $quest->relationship_effects;
        
        // Make enemies
        if (isset($effects['enemy_ids']) && is_array($effects['enemy_ids'])) {
            foreach ($effects['enemy_ids'] as $enemyId) {
                $this->setRelationship($user, $enemyId, 'enemy', -50);
            }
        }
        
        // Make allies
        if (isset($effects['ally_ids']) && is_array($effects['ally_ids'])) {
            foreach ($effects['ally_ids'] as $allyId) {
                if ($allyId) {
                    $this->setRelationship($user, $allyId, 'ally', 30);
                }
            }
        }
    }

    /**
     * Set or update relationship
     */
    private function setRelationship($user, $characterId, $type, $value)
    {
        $relationship = UserCharacterRelationship::firstOrNew([
            'user_id' => $user->id,
            'character_id' => $characterId,
        ]);
        
        $relationship->relationship_type = $type;
        $relationship->relationship_value = max(-100, min(100, $relationship->relationship_value + $value));
        $relationship->save();
    }

    /**
     * Update relationship value
     */
    private function updateRelationship($user, $characterId, $valueChange)
    {
        $relationship = UserCharacterRelationship::firstOrNew([
            'user_id' => $user->id,
            'character_id' => $characterId,
        ]);
        
        $relationship->relationship_value = max(-100, min(100, $relationship->relationship_value + $valueChange));
        
        // Update type based on value
        if ($relationship->relationship_value <= -50) {
            $relationship->relationship_type = 'enemy';
        } elseif ($relationship->relationship_value >= 50) {
            $relationship->relationship_type = 'ally';
        } else {
            $relationship->relationship_type = 'neutral';
        }
        
        $relationship->save();
    }

    /**
     * Check if quest requirements are met
     */
    private function checkRequirements($user, $quest)
    {
        $requirements = $quest->requirements ?? [];
        
        if (isset($requirements['task_count'])) {
            $taskCount = $user->tasks()
                ->where('status', 'done')
                ->whereDate('task_date', '>=', Carbon::parse($user->characterQuests()
                    ->where('character_quest_id', $quest->id)
                    ->first()->accepted_at))
                ->count();
            
            if ($taskCount < $requirements['task_count']) {
                return false;
            }
        }
        
        if (isset($requirements['exercise_count'])) {
            $exerciseCount = $user->exercises()
                ->whereDate('exercise_date', '>=', Carbon::parse($user->characterQuests()
                    ->where('character_quest_id', $quest->id)
                    ->first()->accepted_at))
                ->count();
            
            if ($exerciseCount < $requirements['exercise_count']) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Calculate XP penalty for failing quest (heavier for enemy characters)
     */
    private function calculatePenalty($quest, $user)
    {
        $basePenalty = 50;
        
        // Check if character is enemy
        $relationship = UserCharacterRelationship::where('user_id', $user->id)
            ->where('character_id', $quest->character_id)
            ->first();
        
        if ($relationship && $relationship->relationship_type === 'enemy') {
            // Very heavy penalty for enemy quests
            return $basePenalty * 5; // 250 XP
        }
        
        return $basePenalty;
    }

    /**
     * Calculate currency penalty
     */
    private function calculateCurrencyPenalty($quest, $user)
    {
        $basePenalty = 100;
        
        $relationship = UserCharacterRelationship::where('user_id', $user->id)
            ->where('character_id', $quest->character_id)
            ->first();
        
        if ($relationship && $relationship->relationship_type === 'enemy') {
            return $basePenalty * 3; // 300 linh thạch
        }
        
        return $basePenalty;
    }
}

