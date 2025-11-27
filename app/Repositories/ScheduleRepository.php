<?php

namespace App\Repositories;

use App\Models\Schedule;
use Illuminate\Support\Facades\Log;

class ScheduleRepository extends BaseRepository
{
    /**
     * ScheduleRepository constructor.
     *
     * @param Schedule $model
     */
    public function __construct(Schedule $model)
    {
        parent::__construct($model);
    }

    public function activateSchedule(int $id): bool
    {
        $schedule = $this->find($id);
        if (!$schedule) {
            return false;
        }
        Log::debug('Activating schedule ID: ' . $schedule->id);
        $gene_task = TaskRepository::generateTasksForSchedule($schedule);
        if(!$gene_task) {
            return false;
        }
        $schedule->is_active = true;
        return $schedule->save();
    }

    public function getTasksByScheduleId(int $scheduleId)
    {
        $schedule = $this->find($scheduleId);
        if (!$schedule) {
            return [];
        }
        return $schedule->tasks; // Assuming a Schedule hasMany Tasks relationship
    }
}