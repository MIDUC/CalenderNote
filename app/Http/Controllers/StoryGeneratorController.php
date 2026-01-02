<?php

namespace App\Http\Controllers;

use App\Services\StoryGeneratorService;
use Illuminate\Http\Request;

class StoryGeneratorController extends BaseController
{
    /**
     * Generate a new story for the current user
     */
    public function generate(Request $request)
    {
        $user = $request->user();
        
        try {
            $story = StoryGeneratorService::generateStoryForUser($user, [
                'genre' => $request->input('genre'),
                'source' => $request->input('source'),
            ]);
            
            return $this->sendResponse($story, 'Story generated successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Không thể tạo cốt truyện. Vui lòng thử lại.', [], 500);
        }
    }

    /**
     * Check and auto-generate story for user's current level
     */
    public function checkAndGenerate(Request $request)
    {
        $user = $request->user();
        
        try {
            $story = StoryGeneratorService::checkAndGenerateStoryForLevel($user);
            
            if ($story) {
                return $this->sendResponse($story, 'New story generated for your level!');
            }
            
            return $this->sendResponse(null, 'No new story needed.');
        } catch (\Exception $e) {
            return $this->sendError('Không thể kiểm tra cốt truyện.', [], 500);
        }
    }
}

