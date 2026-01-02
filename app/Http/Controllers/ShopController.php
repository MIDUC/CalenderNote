<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\UserItem;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class ShopController extends BaseController
{
    /**
     * Get all items available in shop
     */
    public function list(Request $request)
    {
        $items = Item::where('price', '>', 0)->orderBy('rarity', 'desc')->orderBy('price', 'asc')->get();
        
        return $this->sendResponse($items, 'Shop items retrieved successfully.');
    }

    /**
     * Buy an item
     */
    public function buy(Request $request, int $itemId)
    {
        $user = $request->user();
        $item = Item::findOrFail($itemId);
        $quantity = $request->input('quantity', 1);

        // Check if user has enough currency
        $totalPrice = $item->price * $quantity;
        if ($user->currency < $totalPrice) {
            return $this->sendError('Không đủ linh thạch để mua vật phẩm này.', [], 400);
        }

        // Deduct currency
        $user->currency -= $totalPrice;
        $user->save();

        // Add item to user's inventory
        $userItem = UserItem::where('user_id', $user->id)
            ->where('item_id', $itemId)
            ->first();
        
        if ($userItem) {
            $userItem->quantity += $quantity;
            $userItem->save();
        } else {
            UserItem::create([
                'user_id' => $user->id,
                'item_id' => $itemId,
                'quantity' => $quantity,
            ]);
        }

        return $this->sendResponse([
            'item' => $item,
            'quantity' => $quantity,
            'remaining_currency' => $user->currency,
        ], 'Mua vật phẩm thành công!');
    }

    /**
     * Sell an item
     */
    public function sell(Request $request, int $itemId)
    {
        $user = $request->user();
        $item = Item::findOrFail($itemId);
        $quantity = $request->input('quantity', 1);

        // Check if user has the item
        $userItem = UserItem::where('user_id', $user->id)
            ->where('item_id', $itemId)
            ->first();
        
        if (!$userItem || $userItem->quantity < $quantity) {
            return $this->sendError('Bạn không có đủ vật phẩm này.', [], 400);
        }

        // Calculate sell price
        $totalPrice = $item->sell_price * $quantity;

        // Add currency
        $user->currency += $totalPrice;
        $user->save();

        // Remove item from inventory
        if ($userItem->quantity <= $quantity) {
            $userItem->delete();
        } else {
            $userItem->quantity -= $quantity;
            $userItem->save();
        }

        return $this->sendResponse([
            'item' => $item,
            'quantity' => $quantity,
            'currency_gained' => $totalPrice,
            'remaining_currency' => $user->currency,
        ], 'Bán vật phẩm thành công!');
    }

    /**
     * Get user's inventory
     */
    public function inventory(Request $request)
    {
        $user = $request->user();
        $userItems = UserItem::where('user_id', $user->id)
            ->with('item')
            ->get()
            ->map(function($userItem) {
                return [
                    'id' => $userItem->item->id,
                    'name' => $userItem->item->name,
                    'description' => $userItem->item->description,
                    'type' => $userItem->item->type,
                    'price' => $userItem->item->price,
                    'sell_price' => $userItem->item->sell_price,
                    'effects' => $userItem->item->effects,
                    'icon' => $userItem->item->icon,
                    'rarity' => $userItem->item->rarity,
                    'pivot' => [
                        'quantity' => $userItem->quantity,
                    ],
                ];
            });
        
        return $this->sendResponse($userItems, 'Inventory retrieved successfully.');
    }
}
