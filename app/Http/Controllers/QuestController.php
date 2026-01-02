<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use App\Models\UserQuest;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class QuestController extends BaseController
{
    /**
     * Get all quests for current user
     */
    public function list(Request $request)
    {
        // Check if user_quests table exists
        if (!Schema::hasTable('user_quests')) {
            return $this->sendResponse([], 'User quests retrieved successfully.');
        }
        
        $user = $request->user();
        
        $userQuests = UserQuest::where('user_id', $user->id)
            ->with(['quest' => function($query) {
                $query->with('npc', 'itemReward');
            }])
            ->get()
            ->map(function($userQuest) {
                $quest = $userQuest->quest;
                return [
                    'id' => $userQuest->id,
                    'quest_id' => $quest->id,
                    'title' => $quest->title,
                    'description' => $quest->description,
                    'type' => $quest->type,
                    'target_count' => $quest->target_count,
                    'progress' => $userQuest->progress,
                    'status' => $userQuest->status,
                    'xp_reward' => $quest->xp_reward,
                    'currency_reward' => $quest->currency_reward,
                    'item_reward' => $quest->itemReward,
                    'item_reward_quantity' => $quest->item_reward_quantity,
                    'npc' => $quest->npc,
                    'completed_at' => $userQuest->completed_at,
                ];
            });
        
        return $this->sendResponse($userQuests, 'User quests retrieved successfully.');
    }

    /**
     * Accept a quest
     */
    public function accept(Request $request, int $questId)
    {
        // Check if user_quests table exists
        if (!Schema::hasTable('user_quests') || !Schema::hasTable('quests')) {
            return $this->sendError('Quest system is not available.', [], 404);
        }
        
        $user = $request->user();
        $quest = Quest::findOrFail($questId);
        
        // Check if user meets level requirement
        if ($quest->level_required > ($user->level ?? 1)) {
            return $this->sendError('Bạn chưa đủ cấp để nhận nhiệm vụ này.', [], 403);
        }
        
        // Check if quest is available
        if ($quest->status !== 'available') {
            return $this->sendError('Nhiệm vụ này không khả dụng.', [], 400);
        }
        
        // Check if user already has this quest
        $existingQuest = UserQuest::where('user_id', $user->id)
            ->where('quest_id', $questId)
            ->first();
        
        if ($existingQuest) {
            return $this->sendError('Bạn đã nhận nhiệm vụ này rồi.', [], 400);
        }
        
        // Create user quest
        $userQuest = UserQuest::create([
            'user_id' => $user->id,
            'quest_id' => $questId,
            'progress' => 0,
            'status' => 'in_progress',
        ]);
        
        return $this->sendResponse($userQuest->load('quest'), 'Quest accepted successfully.');
    }

    /**
     * Update quest progress (called automatically when completing tasks)
     */
    public function updateProgress(Request $request, int $userQuestId)
    {
        // Check if user_quests table exists
        if (!Schema::hasTable('user_quests') || !Schema::hasTable('quests')) {
            return $this->sendError('Quest system is not available.', [], 404);
        }
        
        $user = $request->user();
        $userQuest = UserQuest::where('user_id', $user->id)
            ->findOrFail($userQuestId);
        
        $quest = $userQuest->quest;
        
        // Update progress based on quest type
        $increment = $request->input('increment', 1);
        $userQuest->progress += $increment;
        
        // Check if quest is completed
        if ($userQuest->progress >= $quest->target_count) {
            $userQuest->progress = $quest->target_count;
            $userQuest->status = 'completed';
            $userQuest->completed_at = now();
        }
        
        $userQuest->save();
        
        return $this->sendResponse($userQuest->load('quest'), 'Quest progress updated successfully.');
    }

    /**
     * Claim quest reward
     */
    public function claim(Request $request, int $userQuestId)
    {
        // Check if user_quests table exists
        if (!Schema::hasTable('user_quests') || !Schema::hasTable('quests')) {
            return $this->sendError('Quest system is not available.', [], 404);
        }
        
        $user = $request->user();
        $userQuest = UserQuest::where('user_id', $user->id)
            ->where('status', 'completed')
            ->findOrFail($userQuestId);
        
        $quest = $userQuest->quest;
        
        // Check if already claimed
        if ($userQuest->status === 'claimed') {
            return $this->sendError('Bạn đã nhận thưởng cho nhiệm vụ này rồi.', [], 400);
        }
        
        // Give rewards
        $rewards = [];
        
        if ($quest->xp_reward > 0) {
            $levelResult = GamificationService::addXP($user, $quest->xp_reward);
            $rewards['xp'] = $quest->xp_reward;
            $rewards['level_result'] = $levelResult;
        }
        
        if ($quest->currency_reward > 0) {
            GamificationService::addCurrency($user, $quest->currency_reward);
            $rewards['currency'] = $quest->currency_reward;
        }
        
        if ($quest->item_reward_id && $quest->item_reward_quantity > 0) {
            $userItem = \App\Models\UserItem::where('user_id', $user->id)
                ->where('item_id', $quest->item_reward_id)
                ->first();
            
            if ($userItem) {
                $userItem->quantity += $quest->item_reward_quantity;
                $userItem->save();
            } else {
                \App\Models\UserItem::create([
                    'user_id' => $user->id,
                    'item_id' => $quest->item_reward_id,
                    'quantity' => $quest->item_reward_quantity,
                ]);
            }
            $rewards['item'] = $quest->itemReward;
            $rewards['item_quantity'] = $quest->item_reward_quantity;
        }
        
        // Mark as claimed
        $userQuest->status = 'claimed';
        $userQuest->save();
        
        return $this->sendResponse([
            'user_quest' => $userQuest->load('quest'),
            'rewards' => $rewards,
        ], 'Quest reward claimed successfully.');
    }

    /**
     * Auto-update quest progress when task is completed
     * This should be called from TaskController
     */
    public static function updateQuestProgressOnTaskComplete($user, $task)
    {
        // Check if user_quests table exists
        if (!Schema::hasTable('user_quests') || !Schema::hasTable('quests')) {
            return; // Silently return if tables don't exist
        }
        
        try {
            // Find all active quests of type 'task' for this user
            $userQuests = UserQuest::where('user_id', $user->id)
                ->where('status', 'in_progress')
                ->with('quest')
                ->get();
            
            foreach ($userQuests as $userQuest) {
                $quest = $userQuest->quest;
                
                // Check if this quest is about completing tasks
                if ($quest->type === 'task') {
                    $userQuest->progress += 1;
                    
                    if ($userQuest->progress >= $quest->target_count) {
                        $userQuest->progress = $quest->target_count;
                        $userQuest->status = 'completed';
                        $userQuest->completed_at = now();
                    }
                    
                    $userQuest->save();
                }
            }
        } catch (\Exception $e) {
            // Silently ignore errors if tables don't exist
            \Log::debug('Quest progress update skipped', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
