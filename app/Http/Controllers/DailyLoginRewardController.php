<?php

namespace App\Http\Controllers;

use App\Models\DailyLoginReward;
use App\Models\UserItem;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DailyLoginRewardController extends BaseController
{
    /**
     * Get all daily login rewards
     */
    public function list(Request $request)
    {
        $rewards = DailyLoginReward::orderBy('day')->with('itemReward')->get();
        return $this->sendResponse($rewards, 'Daily login rewards retrieved successfully.');
    }

    /**
     * Check if user can claim today's reward
     */
    public function check(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();
        
        // Ensure current_reward_day is valid (1-7)
        if (!$user->current_reward_day || $user->current_reward_day < 1 || $user->current_reward_day > 7) {
            $user->current_reward_day = 1;
        }
        
        // Check if user already claimed today
        $canClaim = false;
        $nextRewardDay = $user->current_reward_day;
        
        if (!$user->last_reward_claimed_date) {
            // First time, can claim day 1
            $canClaim = true;
            $nextRewardDay = 1;
            $user->current_reward_day = 1;
            $user->login_streak = 1;
        } elseif ($user->last_reward_claimed_date->lt($today)) {
            // Check if it's consecutive day
            $yesterday = $today->copy()->subDay();
            
            if ($user->last_reward_claimed_date->eq($yesterday)) {
                // Consecutive day
                $user->login_streak = ($user->login_streak ?? 0) + 1;
                $nextRewardDay = min(7, $user->current_reward_day + 1);
            } else {
                // Streak broken, reset to day 1
                $user->login_streak = 1;
                $nextRewardDay = 1;
            }
            
            $user->current_reward_day = $nextRewardDay;
            $canClaim = true;
        } else {
            // Already claimed today
            $nextRewardDay = $user->current_reward_day;
        }
        
        // Save user if changed
        if ($canClaim || $user->isDirty()) {
            $user->save();
        }
        
        // Ensure nextRewardDay is valid
        $nextRewardDay = max(1, min(7, $nextRewardDay));
        
        $reward = DailyLoginReward::where('day', $nextRewardDay)->with('itemReward')->first();
        
        return $this->sendResponse([
            'can_claim' => $canClaim,
            'current_day' => $nextRewardDay,
            'login_streak' => $user->login_streak ?? 0,
            'reward' => $reward,
            'last_claimed_date' => $user->last_reward_claimed_date,
        ], 'Reward status retrieved successfully.');
    }

    /**
     * Claim today's login reward
     */
    public function claim(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();
        
        // Check if already claimed today
        if ($user->last_reward_claimed_date && $user->last_reward_claimed_date->eq($today)) {
            return $this->sendError('Bạn đã nhận phần thưởng hôm nay rồi!', [], 400);
        }
        
        // Ensure current_reward_day is valid (1-7)
        if (!$user->current_reward_day || $user->current_reward_day < 1 || $user->current_reward_day > 7) {
            $user->current_reward_day = 1;
        }
        
        // Get reward for current day
        $reward = DailyLoginReward::where('day', $user->current_reward_day)->with('itemReward')->first();
        
        if (!$reward) {
            // If reward not found, try to get day 1 as fallback
            $user->current_reward_day = 1;
            $reward = DailyLoginReward::where('day', 1)->with('itemReward')->first();
            
            if (!$reward) {
                return $this->sendError('Không tìm thấy phần thưởng. Vui lòng liên hệ admin.', [], 404);
            }
        }
        
        $rewards = [];
        
        // Give XP reward
        if ($reward->xp_reward > 0) {
            $levelResult = GamificationService::addXP($user, $reward->xp_reward);
            $rewards['xp'] = $reward->xp_reward;
            $rewards['level_result'] = $levelResult;
        }
        
        // Give currency reward
        if ($reward->currency_reward > 0) {
            GamificationService::addCurrency($user, $reward->currency_reward);
            $rewards['currency'] = $reward->currency_reward;
        }
        
        // Give item reward
        if ($reward->item_reward_id && $reward->item_reward_quantity > 0) {
            $userItem = UserItem::where('user_id', $user->id)
                ->where('item_id', $reward->item_reward_id)
                ->first();
            
            if ($userItem) {
                $userItem->quantity += $reward->item_reward_quantity;
                $userItem->save();
            } else {
                UserItem::create([
                    'user_id' => $user->id,
                    'item_id' => $reward->item_reward_id,
                    'quantity' => $reward->item_reward_quantity,
                ]);
            }
            $rewards['item'] = $reward->itemReward;
            $rewards['item_quantity'] = $reward->item_reward_quantity;
        }
        
        // Update user
        $user->last_reward_claimed_date = $today;
        $user->last_login_date = $today;
        
        // Update current_reward_day for next time (cycle back to 1 after day 7)
        if ($user->current_reward_day >= 7) {
            $user->current_reward_day = 1;
        } else {
            $user->current_reward_day = $user->current_reward_day + 1;
        }
        
        $user->save();
        
        return $this->sendResponse([
            'rewards' => $rewards,
            'current_day' => $user->current_reward_day,
            'login_streak' => $user->login_streak ?? 0,
        ], 'Đã nhận phần thưởng đăng nhập thành công!');
    }
}
