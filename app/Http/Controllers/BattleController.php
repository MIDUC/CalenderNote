<?php

namespace App\Http\Controllers;

use App\Models\Monster;
use App\Models\Battle;
use App\Models\UserItem;
use App\Services\GamificationService;
use App\Services\BattleService;
use App\Services\StoryRuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class BattleController extends BaseController
{
    /**
     * Get all available monsters
     */
    public function list(Request $request)
    {
        // Check if monsters table exists
        if (!Schema::hasTable('monsters')) {
            return $this->sendResponse([], 'Monsters feature is not available.');
        }
        
        try {
            $user = $request->user();
            $monsters = Monster::where('level', '<=', $user->level + 5)
                ->orderBy('level', 'asc')
                ->get()
                ->filter(function($monster) {
                    // Filter by encounter rate
                    return BattleService::shouldEncounter($monster);
                })
                ->values(); // Re-index array
            
            return $this->sendResponse($monsters, 'Monsters retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendResponse([], 'Monsters feature is not available.');
        }
    }

    /**
     * Start a battle with a monster
     */
    public function start(Request $request, int $monsterId)
    {
        // Check if monsters and battles tables exist
        if (!Schema::hasTable('monsters') || !Schema::hasTable('battles')) {
            return $this->sendError('Battle feature is not available.', [], 404);
        }
        
        try {
            $user = $request->user();
            $monster = Monster::findOrFail($monsterId);
            
            // Check if user has an active battle
            $activeBattle = Battle::where('user_id', $user->id)
                ->where('status', 'in_progress')
                ->first();
            
            if ($activeBattle) {
                return $this->sendError('Bạn đang có một trận đấu đang diễn ra.', [], 400);
            }
            
            // Create new battle
            $battle = Battle::create([
                'user_id' => $user->id,
                'monster_id' => $monsterId,
                'status' => 'in_progress',
                'user_hp' => $user->hp,
                'monster_hp' => $monster->hp,
                'turns' => 0,
                'battle_log' => [],
                'started_at' => now(),
            ]);
            
            return $this->sendResponse($battle->load('monster'), 'Battle started successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Không thể bắt đầu trận đấu. Vui lòng thử lại.', [], 500);
        }
    }

    /**
     * Attack in battle
     */
    public function attack(Request $request, int $battleId)
    {
        // Check if battles table exists
        if (!Schema::hasTable('battles')) {
            return $this->sendError('Battle feature is not available.', [], 404);
        }
        
        try {
            $user = $request->user();
            $battle = Battle::where('user_id', $user->id)
                ->where('status', 'in_progress')
                ->findOrFail($battleId);
            
            $monster = $battle->monster;
            $battleLog = $battle->battle_log ?? [];
            
            // User attacks with critical hit chance
            $attackResult = BattleService::calculateDamage($user, $monster, true);
            $userDamage = $attackResult['damage'];
            
            // Check if monster dodges
            $isDodged = BattleService::checkDodge($monster, false);
            
            if ($isDodged) {
                $battleLog[] = [
                    'turn' => $battle->turns + 1,
                    'action' => 'user_attack_dodged',
                    'damage' => 0,
                    'monster_hp' => $battle->monster_hp,
                ];
            } else {
                $battle->monster_hp = max(0, $battle->monster_hp - $userDamage);
                $battleLog[] = [
                    'turn' => $battle->turns + 1,
                    'action' => $attackResult['is_critical'] ? 'user_attack_critical' : 'user_attack',
                    'damage' => $userDamage,
                    'monster_hp' => $battle->monster_hp,
                    'is_critical' => $attackResult['is_critical'],
                    'critical_multiplier' => $attackResult['multiplier'],
                ];
            }
            
            // Check if monster is defeated
            if ($battle->monster_hp <= 0) {
                $battle->status = 'won';
                $battle->ended_at = now();
                $battle->xp_gained = $monster->xp_reward;
                $battle->currency_gained = $monster->currency_reward;
                
                // Give rewards
                GamificationService::addXP($user, $monster->xp_reward);
                GamificationService::addCurrency($user, $monster->currency_reward);
                
                // Check story rules for killing monster
                StoryRuleService::checkRules($user, 'kill_monster', [
                    'monster_id' => $monster->id,
                    'monster_name' => $monster->name,
                ]);
                
                // Handle drop items with drop rate calculation
                $droppedItems = [];
                if ($monster->drop_items && is_array($monster->drop_items)) {
                    foreach ($monster->drop_items as $drop) {
                        if (isset($drop['chance'])) {
                            $baseChance = $drop['chance'];
                            if (BattleService::shouldDropItem($monster, $baseChance)) {
                                $userItem = UserItem::where('user_id', $user->id)
                                    ->where('item_id', $drop['item_id'])
                                    ->first();
                                
                                $quantity = $drop['quantity'] ?? 1;
                                if ($userItem) {
                                    $userItem->quantity += $quantity;
                                    $userItem->save();
                                } else {
                                    UserItem::create([
                                        'user_id' => $user->id,
                                        'item_id' => $drop['item_id'],
                                        'quantity' => $quantity,
                                    ]);
                                }
                                $droppedItems[] = ['item_id' => $drop['item_id'], 'quantity' => $quantity];
                            }
                        }
                    }
                }
                
                // Check for rare drop
                if (BattleService::shouldDropRareItem($monster)) {
                    // Add rare item drop logic here if needed
                    // For now, just log it
                    $battleLog[] = [
                        'turn' => $battle->turns + 1,
                        'action' => 'rare_drop',
                        'message' => 'Đã rơi vật phẩm hiếm!',
                    ];
                }
                
                $battleLog[] = [
                    'turn' => $battle->turns + 1,
                    'action' => 'victory',
                    'xp_gained' => $monster->xp_reward,
                    'currency_gained' => $monster->currency_reward,
                ];
        } else {
            // Monster attacks with critical hit chance
            $attackResult = BattleService::calculateDamage($monster, $user, false);
            $monsterDamage = $attackResult['damage'];
            
            // Check if user dodges
            $isDodged = BattleService::checkDodge($user, true);
            
            if ($isDodged) {
                $battleLog[] = [
                    'turn' => $battle->turns + 1,
                    'action' => 'monster_attack_dodged',
                    'damage' => 0,
                    'user_hp' => $battle->user_hp,
                ];
            } else {
                $battle->user_hp = max(0, $battle->user_hp - $monsterDamage);
                $battleLog[] = [
                    'turn' => $battle->turns + 1,
                    'action' => $attackResult['is_critical'] ? 'monster_attack_critical' : 'monster_attack',
                    'damage' => $monsterDamage,
                    'user_hp' => $battle->user_hp,
                    'is_critical' => $attackResult['is_critical'],
                    'critical_multiplier' => $attackResult['multiplier'],
                ];
            }
                
                // Check if user is defeated
                if ($battle->user_hp <= 0) {
                    $battle->status = 'lost';
                    $battle->ended_at = now();
                    $battleLog[] = [
                        'turn' => $battle->turns + 1,
                        'action' => 'defeat',
                    ];
                    
                    // Update user HP
                    $user->hp = 0;
                    $user->save();
                }
            }
            
            $battle->turns++;
            $battle->battle_log = $battleLog;
            $battle->save();
            
            // Update user HP if still alive
            if ($battle->status === 'in_progress') {
                $user->hp = $battle->user_hp;
                $user->save();
            }
            
            // Check achievements after battle
            if ($battle->status === 'won') {
                \App\Http\Controllers\AchievementController::checkAchievements($user, 'battle');
            }
            
            return $this->sendResponse([
                'battle' => $battle->load('monster'),
                'battle_log' => $battleLog,
            ], 'Attack executed successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Không thể thực hiện tấn công. Vui lòng thử lại.', [], 500);
        }
    }

    /**
     * Flee from battle
     */
    public function flee(Request $request, int $battleId)
    {
        // Check if battles table exists
        if (!Schema::hasTable('battles')) {
            return $this->sendError('Battle feature is not available.', [], 404);
        }
        
        try {
            $user = $request->user();
            $battle = Battle::where('user_id', $user->id)
                ->where('status', 'in_progress')
                ->findOrFail($battleId);
            
            $battle->status = 'fled';
            $battle->ended_at = now();
            $battle->save();
            
            // Update user HP
            $user->hp = $battle->user_hp;
            $user->save();
            
            return $this->sendResponse($battle, 'Fled from battle successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Không thể chạy trốn. Vui lòng thử lại.', [], 500);
        }
    }

    /**
     * Get battle history
     */
    public function history(Request $request)
    {
        // Check if battles table exists
        if (!Schema::hasTable('battles')) {
            return $this->sendResponse([], 'Battle history is not available.');
        }
        
        try {
            $user = $request->user();
            $battles = Battle::where('user_id', $user->id)
                ->with('monster')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();
            
            return $this->sendResponse($battles, 'Battle history retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendResponse([], 'Battle history is not available.');
        }
    }
}
