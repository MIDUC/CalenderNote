<?php

namespace App\Http\Controllers;

use App\Models\StoryCharacter;
use App\Models\UserCharacterRelationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CharacterController extends BaseController
{
    /**
     * Get all characters from a story
     */
    public function listByStory(Request $request, int $storyId)
    {
        $characters = StoryCharacter::where('story_id', $storyId)
            ->where('is_active', true)
            ->with('story')
            ->orderBy('order')
            ->get();
        
        return $this->sendResponse($characters, 'Characters retrieved successfully.');
    }

    /**
     * Get character details with user's relationship
     */
    public function show(Request $request, int $characterId)
    {
        $user = $request->user();
        $character = StoryCharacter::with('story', 'quests')
            ->findOrFail($characterId);
        
        // Get user's relationship with this character
        $relationship = UserCharacterRelationship::where('user_id', $user->id)
            ->where('character_id', $characterId)
            ->first();
        
        $character->user_relationship = $relationship ?? [
            'relationship_type' => 'neutral',
            'relationship_value' => 0,
        ];
        
        return $this->sendResponse($character, 'Character retrieved successfully.');
    }

    /**
     * Get user's relationships with all characters
     */
    public function relationships(Request $request)
    {
        $user = $request->user();
        $relationships = UserCharacterRelationship::where('user_id', $user->id)
            ->with('character.story')
            ->get();
        
        return $this->sendResponse($relationships, 'Character relationships retrieved successfully.');
    }
}

