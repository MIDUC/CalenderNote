<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends BaseController
{
    /**
     * Get leaderboard by level
     */
    public function level(Request $request)
    {
        $users = User::orderBy('level', 'desc')
            ->orderBy('exp', 'desc')
            ->limit(100)
            ->get(['id', 'name', 'level', 'exp', 'level_name']);
        
        return $this->sendResponse($users, 'Level leaderboard retrieved successfully.');
    }

    /**
     * Get leaderboard by tasks completed
     */
    public function tasks(Request $request)
    {
        $users = User::withCount(['tasks' => function($query) {
            $query->where('status', 'done');
        }])
        ->orderBy('tasks_count', 'desc')
        ->limit(100)
        ->get(['id', 'name', 'level', 'tasks_count']);
        
        return $this->sendResponse($users, 'Tasks leaderboard retrieved successfully.');
    }

    /**
     * Get leaderboard by currency
     */
    public function currency(Request $request)
    {
        $users = User::orderBy('currency', 'desc')
            ->limit(100)
            ->get(['id', 'name', 'level', 'currency']);
        
        return $this->sendResponse($users, 'Currency leaderboard retrieved successfully.');
    }

    /**
     * Get leaderboard by streak
     */
    public function streak(Request $request)
    {
        $users = User::orderBy('current_streak', 'desc')
            ->orderBy('longest_streak', 'desc')
            ->limit(100)
            ->get(['id', 'name', 'level', 'current_streak', 'longest_streak']);
        
        return $this->sendResponse($users, 'Streak leaderboard retrieved successfully.');
    }
}
