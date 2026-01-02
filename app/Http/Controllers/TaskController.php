<?php

namespace App\Http\Controllers;

use App\Repositories\TaskRepository;
use Illuminate\Http\Request;
use App\Http\Requests\Task\StoreRequest;
use App\Http\Requests\Task\UpdateRequest;
use App\Services\GamificationService;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\AchievementController;

class TaskController extends BaseController
{
    protected TaskRepository $repository;
    public function __construct(TaskRepository $repository) // dùng thủ công, khởi tạo trong __construct()
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of Tasks.
     */
    public function listing(Request $request)
    {
        $filters = $this->filterParams($request);
        
        // If not admin, only show current user's tasks
        if ($request->user()->role !== 'admin') {
            $filters['filters']['user_id'] = $request->user()->id;
        }

        $data = $this->repository->listing($filters['filters'], $filters['sort_by'], $filters['sort_direction'], $filters['page'], $filters['item_per_page'],['schedule','user']); // 👈 truyền mảng quan hệ cần load

        return $this->sendResponse($data, 'Tasks retrieved successfully.');
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        
        // Auto-set user_id from authenticated user if not admin
        if ($request->user()->role !== 'admin' && !isset($data['user_id'])) {
            $data['user_id'] = $request->user()->id;
        }
        
        $Task = $this->repository->store($data);

        return $this->sendResponse($Task, 'Task created successfully.');
    }

    /**
     * Display a single Task.
     */
    public function show(Request $request, int $id)
    {
        $task = $this->repository->find($id);
        if (!$task) {
            return $this->sendError('Task not found.', [], 404);
        }
        
        // Check permission: only admin or task owner can view
        if ($request->user()->role !== 'admin' && $task->user_id !== $request->user()->id) {
            return $this->sendError('Unauthorized. You can only view your own tasks.', [], 403);
        }
        
        return $this->sendResponse($task->load(['schedule', 'user']), 'Task retrieved successfully.');
    }

    /**
     * Update an existing Task.
     */
    public function update(UpdateRequest $request, int $id)
    {
        // Get task before update to check if status changed
        $task = $this->repository->find($id);
        if (!$task) {
            return $this->sendError('Task not found.', [], 404);
        }
        
        // Check permission: only admin or task owner can update
        if ($request->user()->role !== 'admin' && $task->user_id !== $request->user()->id) {
            return $this->sendError('Unauthorized. You can only update your own tasks.', [], 403);
        }
        
        // Get all input data, not just validated (since UpdateRequest has empty rules)
        $data = $request->all();
        
        \Log::info('Task update request', [
            'id' => $id,
            'data' => $data,
            'validated' => $request->validated(),
        ]);
        
        $wasPending = $task->status === 'pending';
        $isCompleting = isset($data['status']) && $data['status'] === 'done' && $wasPending;
        
        // Auto-set completed_at when status is 'done'
        if (isset($data['status'])) {
            if ($data['status'] === 'done' && !isset($data['completed_at'])) {
                $data['completed_at'] = now();
            } elseif ($data['status'] !== 'done') {
                // Clear completed_at if status is not 'done'
                $data['completed_at'] = null;
            }
        }
        
        \Log::info('Task update data after processing', ['data' => $data]);
        
        $updated = $this->repository->update($id, $data);

        if (!$updated) {
            \Log::error('Task update failed', ['id' => $id, 'data' => $data]);
            return $this->sendError('Task not found or update failed.', [], 404);
        }

        $updatedTask = $this->repository->find($id);
        
        // Reward user if task was just completed
        $rewardData = null;
        $streakData = null;
        if ($isCompleting) {
            $user = $request->user();
            if ($user) {
                // Update streak first
                $streakData = GamificationService::updateStreak($user);
                
                // Check for milestone reward
                $milestoneReward = GamificationService::getStreakMilestoneReward($streakData['current_streak']);
                if ($milestoneReward) {
                    GamificationService::addXP($user, $milestoneReward['xp']);
                    GamificationService::addCurrency($user, $milestoneReward['currency']);
                    $streakData['milestone_reward'] = $milestoneReward;
                }
                
                // Calculate rewards with streak bonus
                $rewardData = GamificationService::rewardTaskCompletion($user, [
                    'xp_bonus' => $streakData['streak_bonus_xp'],
                    'currency_bonus' => $streakData['streak_bonus_currency'],
                ]);
                
                // Update quest progress
                QuestController::updateQuestProgressOnTaskComplete($user, $updatedTask);
                
                // Check achievements
                AchievementController::checkAchievements($user, 'task');
                
                \Log::info('Task completion reward', [
                    'user_id' => $user->id,
                    'task_id' => $id,
                    'reward' => $rewardData,
                    'streak' => $streakData,
                ]);
            }
        }
        
        \Log::info('Task updated successfully', ['id' => $id, 'task' => $updatedTask]);
        
        $response = [
            'task' => $updatedTask,
        ];
        
        if ($rewardData) {
            $response['reward'] = $rewardData;
            
            // Include generated story info if available
            if (isset($rewardData['level_result']['generated_story']) && $rewardData['level_result']['generated_story']) {
                $response['generated_story'] = $rewardData['level_result']['generated_story'];
            }
        }
        
        if ($streakData) {
            $response['streak'] = $streakData;
        }
        
        return $this->sendResponse($response, 'Task updated successfully.');
    }

    /**
     * Delete a Task.
     */
    public function delete(Request $request, int $id)
    {
        $task = $this->repository->find($id);
        if (!$task) {
            return $this->sendError('Task not found.', [], 404);
        }
        
        // Check permission: only admin or task owner can delete
        if ($request->user()->role !== 'admin' && $task->user_id !== $request->user()->id) {
            return $this->sendError('Unauthorized. You can only delete your own tasks.', [], 403);
        }
        
        $deleted = $this->repository->delete($id);

        if (!$deleted) {
            return $this->sendError('Delete failed.', [], 500);
        }

        return $this->sendResponse([], 'Task deleted successfully.');
    }
}
