<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\UserEquipment;
use App\Models\UserItem;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class EquipmentController extends BaseController
{
    /**
     * Get user's current equipment
     */
    public function list(Request $request)
    {
        $user = $request->user();
        $equipment = $user->equipment()->with('item')->get()->keyBy('slot');
        
        return $this->sendResponse($equipment, 'Equipment retrieved successfully.');
    }

    /**
     * Equip an item
     */
    public function equip(Request $request, int $itemId)
    {
        $user = $request->user();
        $item = Item::findOrFail($itemId);
        
        // Check if item is equipment type
        if ($item->type !== 'equipment') {
            return $this->sendError('Vật phẩm này không thể trang bị.', [], 400);
        }
        
        // Check if user owns the item
        $userItem = UserItem::where('user_id', $user->id)
            ->where('item_id', $itemId)
            ->first();
        
        if (!$userItem || $userItem->quantity < 1) {
            return $this->sendError('Bạn không có vật phẩm này.', [], 400);
        }
        
        // Determine slot from item effects or default to weapon
        $slot = $item->effects['slot'] ?? 'weapon';
        if (!in_array($slot, ['weapon', 'armor', 'accessory'])) {
            $slot = 'weapon';
        }
        
        // Check if slot is already equipped
        $existingEquipment = UserEquipment::where('user_id', $user->id)
            ->where('slot', $slot)
            ->first();
        
        if ($existingEquipment) {
            // Unequip existing item
            $existingEquipment->delete();
        }
        
        // Equip new item
        $equipment = UserEquipment::create([
            'user_id' => $user->id,
            'item_id' => $itemId,
            'slot' => $slot,
        ]);
        
        // Calculate and update user stats
        $this->updateUserStats($user);
        
        return $this->sendResponse([
            'equipment' => $equipment->load('item'),
            'stats' => [
                'hp' => $user->hp,
                'max_hp' => $user->max_hp,
                'attack' => $user->attack,
                'defense' => $user->defense,
            ],
        ], 'Trang bị thành công!');
    }

    /**
     * Unequip an item
     */
    public function unequip(Request $request, string $slot)
    {
        $user = $request->user();
        
        if (!in_array($slot, ['weapon', 'armor', 'accessory'])) {
            return $this->sendError('Slot không hợp lệ.', [], 400);
        }
        
        $equipment = UserEquipment::where('user_id', $user->id)
            ->where('slot', $slot)
            ->first();
        
        if (!$equipment) {
            return $this->sendError('Không có vật phẩm nào được trang bị ở slot này.', [], 404);
        }
        
        $equipment->delete();
        
        // Recalculate user stats
        $this->updateUserStats($user);
        
        return $this->sendResponse([
            'stats' => [
                'hp' => $user->hp,
                'max_hp' => $user->max_hp,
                'attack' => $user->attack,
                'defense' => $user->defense,
            ],
        ], 'Gỡ trang bị thành công!');
    }

    /**
     * Calculate and update user stats based on equipment
     */
    private function updateUserStats($user)
    {
        // Base stats
        $baseHp = 100;
        $baseMaxHp = 100;
        $baseAttack = 10;
        $baseDefense = 5;
        
        // Get all equipped items
        $equipment = $user->equipment()->with('item')->get();
        
        // Calculate bonuses from equipment
        $hpBonus = 0;
        $maxHpBonus = 0;
        $attackBonus = 0;
        $defenseBonus = 0;
        
        foreach ($equipment as $eq) {
            $item = $eq->item;
            if ($item->effects) {
                $hpBonus += $item->effects['hp'] ?? 0;
                $maxHpBonus += $item->effects['max_hp'] ?? 0;
                $attackBonus += $item->effects['attack'] ?? 0;
                $defenseBonus += $item->effects['defense'] ?? 0;
            }
        }
        
        // Update user stats
        $user->max_hp = $baseMaxHp + $maxHpBonus;
        $user->attack = $baseAttack + $attackBonus;
        $user->defense = $baseDefense + $defenseBonus;
        
        // Adjust current HP if max HP changed
        if ($user->hp > $user->max_hp) {
            $user->hp = $user->max_hp;
        }
        
        $user->save();
    }
}
