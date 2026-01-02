<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Note;
use App\Models\Quest;
use App\Models\Battle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends BaseController
{
    /**
     * Get user statistics
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Task statistics
        $taskStats = [
            'total' => Task::where('user_id', $user->id)->count(),
            'completed' => Task::where('user_id', $user->id)->where('status', 'done')->count(),
            'failed' => Task::where('user_id', $user->id)->where('status', 'failed')->count(),
            'pending' => Task::where('user_id', $user->id)->where('status', 'pending')->count(),
        ];
        
        // Note statistics
        $noteStats = [
            'total' => Note::where('user_id', $user->id)->count(),
        ];
        
        // Quest statistics
        $questStats = [
            'total' => $user->quests()->count(),
            'completed' => $user->quests()->where('status', 'claimed')->count(),
            'in_progress' => $user->quests()->where('status', 'in_progress')->count(),
        ];
        
        // Battle statistics
        $battleStats = [
            'total' => Battle::where('user_id', $user->id)->count(),
            'won' => Battle::where('user_id', $user->id)->where('status', 'won')->count(),
            'lost' => Battle::where('user_id', $user->id)->where('status', 'lost')->count(),
            'total_xp_gained' => Battle::where('user_id', $user->id)->sum('xp_gained'),
            'total_currency_gained' => Battle::where('user_id', $user->id)->sum('currency_gained'),
        ];
        
        // Daily activity (last 7 days)
        $dailyActivity = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $dailyActivity[] = [
                'date' => $date,
                'tasks_completed' => Task::where('user_id', $user->id)
                    ->where('status', 'done')
                    ->whereDate('completed_at', $date)
                    ->count(),
                'notes_created' => Note::where('user_id', $user->id)
                    ->whereDate('created_at', $date)
                    ->count(),
            ];
        }
        
        return $this->sendResponse([
            'user' => [
                'level' => $user->level,
                'exp' => $user->exp,
                'currency' => $user->currency,
                'current_streak' => $user->current_streak,
                'longest_streak' => $user->longest_streak,
            ],
            'tasks' => $taskStats,
            'notes' => $noteStats,
            'quests' => $questStats,
            'battles' => $battleStats,
            'daily_activity' => $dailyActivity,
        ], 'Statistics retrieved successfully.');
    }
}
