<?php

namespace App\Services;

use App\Models\ExerciseSchedule;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExerciseTaskService
{
    /**
     * Accept exercise schedule and create tasks for one month
     */
    public static function acceptScheduleAndCreateTasks(ExerciseSchedule $exerciseSchedule): array
    {
        $user = $exerciseSchedule->user;
        $exerciseType = $exerciseSchedule->exerciseType;
        $today = Carbon::today();
        
        // Calculate date range (one month from start_date or today, whichever is later)
        $startDate = Carbon::parse($exerciseSchedule->start_date);
        if ($startDate->lt($today)) {
            $startDate = $today;
        }
        $endDate = $startDate->copy()->endOfMonth();
        
        // If schedule has end_date and it's before end of month, use it
        if ($exerciseSchedule->end_date) {
            $scheduleEndDate = Carbon::parse($exerciseSchedule->end_date);
            if ($scheduleEndDate->lt($endDate)) {
                $endDate = $scheduleEndDate;
            }
        }
        
        $tasksCreated = [];
        $currentDate = $startDate->copy();
        
        DB::beginTransaction();
        try {
            while ($currentDate->lte($endDate)) {
                $shouldCreateTask = false;
                
                // Check frequency
                if ($exerciseSchedule->frequency === 'daily') {
                    $shouldCreateTask = true;
                } elseif ($exerciseSchedule->frequency === 'weekly') {
                    $dayOfWeek = $currentDate->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.
                    $days = $exerciseSchedule->days_of_week ?? [];
                    $shouldCreateTask = in_array($dayOfWeek, $days);
                }
                
                if ($shouldCreateTask) {
                    // Check if task already exists for this date and exercise
                    $taskTitle = $exerciseType->name . ' - ' . $exerciseSchedule->target_amount . ' ' . $exerciseType->unit;
                    $existingTask = Task::where('user_id', $user->id)
                        ->whereDate('task_date', $currentDate)
                        ->where('title', $taskTitle)
                        ->first();
                    
                    if (!$existingTask) {
                        // Create task directly (without Schedule)
                        $task = Task::create([
                            'user_id' => $user->id,
                            'schedule_id' => null, // ExerciseSchedule is separate from Schedule
                            'title' => $taskTitle,
                            'description' => 'Nhiệm vụ luyện tập: ' . ($exerciseType->description ?? '') . 
                                           ' | Mục tiêu: ' . $exerciseSchedule->target_amount . ' ' . $exerciseType->unit,
                            'task_date' => $currentDate->toDateString(),
                            'status' => 'pending',
                            'require_checkin' => false,
                        ]);
                        
                        $tasksCreated[] = $task;
                    }
                }
                
                $currentDate->addDay();
            }
            
            DB::commit();
            
            Log::info('Exercise schedule accepted and tasks created', [
                'schedule_id' => $exerciseSchedule->id,
                'tasks_created' => count($tasksCreated),
                'date_range' => [$startDate->toDateString(), $endDate->toDateString()],
            ]);
            
            return [
                'success' => true,
                'tasks_created' => count($tasksCreated),
                'tasks' => $tasksCreated,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating tasks from exercise schedule', [
                'schedule_id' => $exerciseSchedule->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}

