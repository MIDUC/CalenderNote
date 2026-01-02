<?php

namespace App\Http\Controllers;

use App\Models\Outfit;
use App\Models\UserOutfit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutfitController extends BaseController
{
    /**
     * Get all available outfits
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $outfits = Outfit::where('is_active', true)
            ->orderBy('order')
            ->orderBy('category')
            ->get();
        
        // Mark which outfits user owns
        if ($user) {
            $ownedOutfitIds = $user->outfits()->pluck('outfits.id')->toArray();
            foreach ($outfits as $outfit) {
                $outfit->is_owned = in_array($outfit->id, $ownedOutfitIds);
                $outfit->is_equipped = $user->current_outfit_id === $outfit->id;
            }
        }
        
        return $this->sendResponse($outfits, 'Outfits retrieved successfully.');
    }

    /**
     * Get user's owned outfits
     */
    public function myOutfits(Request $request)
    {
        $user = $request->user();
        
        $outfits = $user->outfits()
            ->with('outfit')
            ->orderBy('purchased_at', 'desc')
            ->get()
            ->map(function ($userOutfit) use ($user) {
                $outfit = $userOutfit->outfit;
                $outfit->is_equipped = $user->current_outfit_id === $outfit->id;
                $outfit->purchased_at = $userOutfit->purchased_at;
                return $outfit;
            });
        
        return $this->sendResponse($outfits, 'User outfits retrieved successfully.');
    }

    /**
     * Purchase an outfit
     */
    public function purchase(Request $request, $id)
    {
        $user = $request->user();
        $outfit = Outfit::findOrFail($id);
        
        // Check if already owned
        if ($user->outfits()->where('outfit_id', $id)->exists()) {
            return $this->sendError('You already own this outfit.', [], 400);
        }
        
        // Check level requirement
        if ($user->level < $outfit->level_required) {
            return $this->sendError("You need to be level {$outfit->level_required} to purchase this outfit.", [], 400);
        }
        
        // Check if limited and expired
        if ($outfit->is_limited && $outfit->limited_until && now() > $outfit->limited_until) {
            return $this->sendError('This limited outfit is no longer available.', [], 400);
        }
        
        // Check currency
        if ($outfit->price_type === 0) {
            // Regular currency
            if ($user->currency < $outfit->price) {
                return $this->sendError('Insufficient currency.', [], 400);
            }
            $user->currency -= $outfit->price;
        } else {
            // Premium currency (if implemented)
            return $this->sendError('Premium currency not implemented yet.', [], 400);
        }
        
        DB::beginTransaction();
        try {
            $user->save();
            
            // Add to user's collection
            $user->outfits()->attach($id, [
                'purchased_at' => now(),
                'is_equipped' => false,
            ]);
            
            DB::commit();
            
            return $this->sendResponse($outfit, 'Outfit purchased successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to purchase outfit.', [], 500);
        }
    }

    /**
     * Equip an outfit
     */
    public function equip(Request $request, $id)
    {
        $user = $request->user();
        
        // Check if user owns this outfit
        if (!$user->outfits()->where('outfit_id', $id)->exists()) {
            return $this->sendError('You do not own this outfit.', [], 400);
        }
        
        $outfit = Outfit::findOrFail($id);
        
        // Check level requirement
        if ($user->level < $outfit->level_required) {
            return $this->sendError("You need to be level {$outfit->level_required} to equip this outfit.", [], 400);
        }
        
        $user->current_outfit_id = $id;
        $user->save();
        
        // Update pivot table
        $user->outfits()->updateExistingPivot($id, ['is_equipped' => true]);
        
        // Unequip other outfits
        $user->outfits()->where('outfit_id', '!=', $id)->update(['is_equipped' => false]);
        
        return $this->sendResponse($outfit->load('currentOutfit'), 'Outfit equipped successfully.');
    }

    /**
     * Unequip current outfit
     */
    public function unequip(Request $request)
    {
        $user = $request->user();
        
        if ($user->current_outfit_id) {
            $user->outfits()->updateExistingPivot($user->current_outfit_id, ['is_equipped' => false]);
            $user->current_outfit_id = null;
            $user->save();
        }
        
        return $this->sendResponse(null, 'Outfit unequipped successfully.');
    }
}
