<?php

namespace App\Http\Controllers;

use App\Models\ExerciseType;
use App\Models\UserExercise;
use App\Models\ExerciseSchedule;
use App\Services\GamificationService;
use App\Services\ExerciseTaskService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExerciseController extends BaseController
{
    /**
     * Get all exercise types
     */
    public function types(Request $request)
    {
        $types = ExerciseType::where('is_active', true)
            ->orderBy('order')
            ->get();
        
        return $this->sendResponse($types, 'Exercise types retrieved successfully.');
    }

    /**
     * Get user's exercise schedules
     */
    public function schedules(Request $request)
    {
        $user = $request->user();
        $schedules = $user->exerciseSchedules()
            ->where('is_active', true)
            ->with('exerciseType')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($schedule) use ($user) {
                // Check if tasks exist for this schedule in current month
                $today = Carbon::today();
                $startOfMonth = $today->copy()->startOfMonth();
                $endOfMonth = $today->copy()->endOfMonth();
                
                $exerciseType = $schedule->exerciseType;
                $taskTitle = $exerciseType->name . ' - ' . $schedule->target_amount . ' ' . $exerciseType->unit;
                
                $hasTasks = \App\Models\Task::where('user_id', $user->id)
                    ->where('title', $taskTitle)
                    ->whereBetween('task_date', [$startOfMonth, $endOfMonth])
                    ->exists();
                
                $schedule->is_accepted = $hasTasks;
                return $schedule;
            });
        
        return $this->sendResponse($schedules, 'Exercise schedules retrieved successfully.');
    }

    /**
     * Create exercise schedule and automatically create tasks for one month
     */
    public function createSchedule(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'exercise_type_id' => 'required|exists:exercise_types,id',
            'target_amount' => 'required|numeric|min:0.1',
            'frequency' => 'required|in:daily,weekly,custom',
            'days_of_week' => 'nullable|array',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);
        
        $schedule = ExerciseSchedule::create([
            'user_id' => $user->id,
            'exercise_type_id' => $validated['exercise_type_id'],
            'target_amount' => $validated['target_amount'],
            'frequency' => $validated['frequency'],
            'days_of_week' => $validated['days_of_week'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
        ]);
        
        // Automatically create tasks for one month when creating schedule
        try {
            $result = ExerciseTaskService::acceptScheduleAndCreateTasks($schedule);
            
            return $this->sendResponse([
                'schedule' => $schedule->load('exerciseType'),
                'tasks_created' => $result['tasks_created'],
                'message' => 'Đã tạo lịch tập và tự động tạo ' . $result['tasks_created'] . ' nhiệm vụ cho tháng này!',
            ], 'Exercise schedule created and tasks generated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error creating tasks when creating schedule', [
                'schedule_id' => $schedule->id,
                'error' => $e->getMessage(),
            ]);
            
            // Still return success for schedule creation, but note task creation failed
            return $this->sendResponse([
                'schedule' => $schedule->load('exerciseType'),
                'tasks_created' => 0,
                'message' => 'Đã tạo lịch tập, nhưng có lỗi khi tạo nhiệm vụ. Vui lòng chấp nhận lịch tập sau.',
            ], 'Exercise schedule created, but task generation failed.');
        }
    }

    /**
     * Get today's exercise tasks
     */
    public function todayTasks(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();
        
        $schedules = $user->exerciseSchedules()
            ->where('is_active', true)
            ->where('start_date', '<=', $today)
            ->where(function($query) use ($today) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $today);
            })
            ->with('exerciseType')
            ->get()
            ->filter(function($schedule) use ($today) {
                if ($schedule->frequency === 'daily') {
                    return true;
                } elseif ($schedule->frequency === 'weekly') {
                    $dayOfWeek = $today->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.
                    $days = $schedule->days_of_week ?? [];
                    return in_array($dayOfWeek, $days);
                }
                return false;
            })
            ->map(function($schedule) use ($user, $today) {
                // Check if already completed today
                $completed = UserExercise::where('user_id', $user->id)
                    ->where('exercise_type_id', $schedule->exercise_type_id)
                    ->whereDate('exercise_date', $today)
                    ->sum('amount');
                
                return [
                    'schedule_id' => $schedule->id,
                    'exercise_type' => $schedule->exerciseType,
                    'target_amount' => $schedule->target_amount,
                    'completed_amount' => $completed,
                    'progress_percentage' => min(100, ($completed / $schedule->target_amount) * 100),
                    'is_completed' => $completed >= $schedule->target_amount,
                ];
            });
        
        return $this->sendResponse($schedules->values(), 'Today\'s exercise tasks retrieved successfully.');
    }

    /**
     * Log exercise
     */
    public function log(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'exercise_type_id' => 'required|exists:exercise_types,id',
            'amount' => 'required|numeric|min:0.1',
            'exercise_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $exerciseType = ExerciseType::findOrFail($validated['exercise_type_id']);
        $exerciseDate = $validated['exercise_date'] ? Carbon::parse($validated['exercise_date']) : Carbon::today();
        
        // Calculate rewards (base + bonus based on amount)
        $baseXP = $exerciseType->base_xp;
        $baseCurrency = $exerciseType->base_currency;
        
        // Bonus: 1% per unit above base target
        $targets = $exerciseType->targets ?? [];
        $baseTarget = $targets['level_1'] ?? 10;
        $bonusMultiplier = max(1, 1 + ($validated['amount'] / max(1, $baseTarget)) * 0.01);
        
        $xpGained = (int) ($baseXP * $bonusMultiplier);
        $currencyGained = (int) ($baseCurrency * $bonusMultiplier);
        
        // Create exercise log
        $exercise = UserExercise::create([
            'user_id' => $user->id,
            'exercise_type_id' => $validated['exercise_type_id'],
            'amount' => $validated['amount'],
            'exercise_date' => $exerciseDate,
            'notes' => $validated['notes'] ?? null,
            'xp_gained' => $xpGained,
            'currency_gained' => $currencyGained,
        ]);
        
        // Give rewards
        GamificationService::addXP($user, $xpGained);
        GamificationService::addCurrency($user, $currencyGained);
        
        // Update schedule streak if applicable
        $schedule = ExerciseSchedule::where('user_id', $user->id)
            ->where('exercise_type_id', $validated['exercise_type_id'])
            ->where('is_active', true)
            ->first();
        
        if ($schedule) {
            $todayTotal = UserExercise::where('user_id', $user->id)
                ->where('exercise_type_id', $validated['exercise_type_id'])
                ->whereDate('exercise_date', $exerciseDate)
                ->sum('amount');
            
            if ($todayTotal >= $schedule->target_amount) {
                $schedule->current_streak++;
                $schedule->save();
            }
        }
        
        // Update task streak (if exercise is logged today)
        if ($exerciseDate->isToday()) {
            GamificationService::updateStreak($user);
        }
        
        return $this->sendResponse([
            'exercise' => $exercise->load('exerciseType'),
            'rewards' => [
                'xp' => $xpGained,
                'currency' => $currencyGained,
            ],
        ], 'Exercise logged successfully!');
    }

    /**
     * Get exercise history
     */
    public function history(Request $request)
    {
        $user = $request->user();
        $exercises = $user->exercises()
            ->with('exerciseType')
            ->orderBy('exercise_date', 'desc')
            ->limit(50)
            ->get();
        
        return $this->sendResponse($exercises, 'Exercise history retrieved successfully.');
    }

    /**
     * Accept exercise schedule and create tasks for one month
     */
    public function acceptSchedule(Request $request, int $scheduleId)
    {
        $user = $request->user();
        
        $schedule = ExerciseSchedule::where('user_id', $user->id)
            ->where('id', $scheduleId)
            ->with('exerciseType')
            ->firstOrFail();
        
        try {
            $result = ExerciseTaskService::acceptScheduleAndCreateTasks($schedule);
            
            return $this->sendResponse([
                'schedule' => $schedule,
                'tasks_created' => $result['tasks_created'],
                'start_date' => $result['start_date'],
                'end_date' => $result['end_date'],
                'message' => 'Đã chấp nhận lịch tập và tạo ' . $result['tasks_created'] . ' nhiệm vụ cho tháng này!',
            ], 'Exercise schedule accepted and tasks created successfully.');
        } catch (\Exception $e) {
            \Log::error('Error accepting exercise schedule', [
                'schedule_id' => $scheduleId,
                'error' => $e->getMessage(),
            ]);
            
            return $this->sendError('Không thể tạo nhiệm vụ. Vui lòng thử lại.', [], 500);
        }
    }
}
