<?php

namespace App\Http\Controllers;

use App\Models\NPC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class NPCController extends BaseController
{
    /**
     * Get all active NPCs
     */
    public function list(Request $request)
    {
        // Check if npcs table exists
        if (!Schema::hasTable('npcs')) {
            return $this->sendResponse([], 'NPCs feature is not available.');
        }
        
        $user = $request->user();
        
        try {
            $npcs = NPC::where('is_active', true)
                ->where('level_required', '<=', $user->level ?? 1);
            
            // Only load quests if quests table exists
            if (Schema::hasTable('quests')) {
                $npcs = $npcs->with('quests');
            }
            
            $npcs = $npcs->get();
            
            return $this->sendResponse($npcs, 'NPCs retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendResponse([], 'NPCs feature is not available.');
        }
    }

    /**
     * Get NPC details with available quests
     */
    public function show(Request $request, int $id)
    {
        // Check if npcs table exists
        if (!Schema::hasTable('npcs')) {
            return $this->sendError('NPCs feature is not available.', [], 404);
        }
        
        try {
            $user = $request->user();
            $npc = NPC::query();
            
            // Only load quests if quests table exists
            if (Schema::hasTable('quests')) {
                $npc = $npc->with(['quests' => function($query) use ($user) {
                    $query->where('level_required', '<=', $user->level ?? 1)
                          ->where('status', 'available');
                }]);
            }
            
            $npc = $npc->findOrFail($id);
            
            // Check if user can interact with this NPC
            if ($npc->level_required > ($user->level ?? 1)) {
                return $this->sendError('Bạn chưa đủ cấp để tương tác với NPC này.', [], 403);
            }
            
            return $this->sendResponse($npc, 'NPC retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('NPC not found or feature is not available.', [], 404);
        }
    }

    /**
     * Interact with NPC (get dialogue)
     */
    public function interact(Request $request, int $id)
    {
        // Check if npcs table exists
        if (!Schema::hasTable('npcs')) {
            return $this->sendError('NPCs feature is not available.', [], 404);
        }
        
        try {
            $user = $request->user();
            $npc = NPC::findOrFail($id);
            
            // Check if user can interact
            if ($npc->level_required > ($user->level ?? 1)) {
                return $this->sendError('Bạn chưa đủ cấp để tương tác với NPC này.', [], 403);
            }
            
            $availableQuests = [];
            
            // Get available quests only if quests table exists
            if (Schema::hasTable('quests')) {
                try {
                    $availableQuests = $npc->quests()
                        ->where('level_required', '<=', $user->level ?? 1)
                        ->where('status', 'available');
                    
                    // Only check user_quests if table exists
                    if (Schema::hasTable('user_quests')) {
                        $availableQuests = $availableQuests->whereDoesntHave('users', function($query) use ($user) {
                            $query->where('user_id', $user->id);
                        });
                    }
                    
                    $availableQuests = $availableQuests->get();
                } catch (\Exception $e) {
                    // Silently ignore if quests can't be loaded
                    $availableQuests = [];
                }
            }
            
            return $this->sendResponse([
                'npc' => $npc,
                'dialogue' => $npc->dialogue,
                'available_quests' => $availableQuests,
            ], 'NPC interaction successful.');
        } catch (\Exception $e) {
            return $this->sendError('NPC not found or feature is not available.', [], 404);
        }
    }
}
