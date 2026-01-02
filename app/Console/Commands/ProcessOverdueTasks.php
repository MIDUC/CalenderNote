<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Services\GamificationService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessOverdueTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:process-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process overdue tasks: mark as failed and subtract XP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        
        // Find all pending tasks that are overdue (task_date < today)
        $overdueTasks = Task::where('status', 'pending')
            ->whereDate('task_date', '<', $today)
            ->with('user')
            ->get();
        
        $this->info("Found {$overdueTasks->count()} overdue tasks to process.");
        
        $processedCount = 0;
        $xpPenalty = 5; // Small XP penalty for each failed task
        
        foreach ($overdueTasks as $task) {
            try {
                // Mark task as failed
                $task->status = 'failed';
                $task->save();
                
                // Subtract XP from user (safely - won't go below 0, won't level down)
                if ($task->user) {
                    $result = GamificationService::subtractXP($task->user, $xpPenalty);
                    
                    Log::info('Overdue task processed', [
                        'task_id' => $task->id,
                        'user_id' => $task->user_id,
                        'task_date' => $task->task_date,
                        'xp_subtracted' => $result['xp_subtracted'],
                        'user_exp_after' => $result['new_exp'],
                    ]);
                }
                
                $processedCount++;
            } catch (\Exception $e) {
                Log::error('Error processing overdue task', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage(),
                ]);
                
                $this->error("Failed to process task ID {$task->id}: {$e->getMessage()}");
            }
        }
        
        $this->info("Successfully processed {$processedCount} overdue tasks.");
        
        return Command::SUCCESS;
    }
}

