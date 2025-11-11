<?php

namespace App\Http\Controllers;

use App\Repositories\ScheduleRepository;
use Illuminate\Http\Request;
use App\Http\Requests\Schedule\StoreRequest;
use App\Http\Requests\Schedule\UpdateRequest;

class ScheduleController extends BaseController
{
    protected ScheduleRepository $repository;
    public function __construct(ScheduleRepository $repository) // dùng thủ công, khởi tạo trong __construct()
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of schedules.
     */
    public function listing(Request $request)
    {
        $filters = $this->filterParams($request);

        $data = $this->repository->listing($filters['filters'], $filters['sort_by'], $filters['sort_direction'], $filters['page'], $filters['item_per_page']);

        return $this->sendResponse($data, 'Schedules retrieved successfully.');
    }
    
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $schedule = $this->repository->store($data);

        return $this->sendResponse($schedule, 'Schedule created successfully.');
    }

    /**
     * Update an existing schedule.
     */
    public function update(UpdateRequest $request, int $id)
    {
        $updated = $this->repository->update($id, $request->validated());

        if (!$updated) {
            return $this->sendError('Schedule not found or update failed.', [], 404);
        }

        return $this->sendResponse($this->repository->find($id), 'Schedule updated successfully.');
    }

    /**
     * Delete a schedule.
     */
    public function delete(int $id)
    {
        $deleted = $this->repository->delete($id);

        if (!$deleted) {
            return $this->sendError('Schedule not found or delete failed.', [], 404);
        }

        return $this->sendResponse([], 'Schedule deleted successfully.');
    }

    public function play(int $id)
    {
        $played = $this->repository->activateSchedule($id);

        if (!$played) {
            return $this->sendError('Schedule not found or play failed.', [], 404);
        }

        return $this->sendResponse([], 'Schedule played successfully.');
    }
}
