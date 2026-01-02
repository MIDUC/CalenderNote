<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\Request;

class StoryController extends BaseController
{
    /**
     * Get all available stories
     */
    public function list(Request $request)
    {
        $user = $request->user();
        $stories = Story::where('is_active', true)
            ->where('unlock_level', '<=', $user->level)
            ->with('characters')
            ->orderBy('order')
            ->get();
        
        return $this->sendResponse($stories, 'Stories retrieved successfully.');
    }

    /**
     * Get story details
     */
    public function show(Request $request, int $id)
    {
        $story = Story::with('characters')
            ->findOrFail($id);
        
        return $this->sendResponse($story, 'Story retrieved successfully.');
    }
}

