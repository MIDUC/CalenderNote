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
        
        // If not admin, only show current user's schedules
        if ($request->user()->role !== 'admin') {
            $filters['filters']['user_id'] = $request->user()->id;
        }

        $data = $this->repository->listing($filters['filters'], $filters['sort_by'], $filters['sort_direction'], $filters['page'], $filters['item_per_page']);

        return $this->sendResponse($data, 'Schedules retrieved successfully.');
    }
    
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        
        // Auto-set user_id from authenticated user if not admin
        if ($request->user()->role !== 'admin' && !isset($data['user_id'])) {
            $data['user_id'] = $request->user()->id;
        }
        
        $schedule = $this->repository->store($data);

        return $this->sendResponse($schedule, 'Schedule created successfully.');
    }

    /**
     * Display a single Schedule.
     */
    public function show(Request $request, int $id)
    {
        $schedule = $this->repository->find($id);
        if (!$schedule) {
            return $this->sendError('Schedule not found.', [], 404);
        }
        
        // Check permission: only admin or schedule owner can view
        if ($request->user()->role !== 'admin' && $schedule->user_id !== $request->user()->id) {
            return $this->sendError('Unauthorized. You can only view your own schedules.', [], 403);
        }
        
        return $this->sendResponse($schedule->load('user'), 'Schedule retrieved successfully.');
    }

    /**
     * Update an existing schedule.
     */
    public function update(UpdateRequest $request, int $id)
    {
        $schedule = $this->repository->find($id);
        if (!$schedule) {
            return $this->sendError('Schedule not found.', [], 404);
        }
        
        // Check permission: only admin or schedule owner can update
        if ($request->user()->role !== 'admin' && $schedule->user_id !== $request->user()->id) {
            return $this->sendError('Unauthorized. You can only update your own schedules.', [], 403);
        }
        
        $updated = $this->repository->update($id, $request->validated());

        if (!$updated) {
            return $this->sendError('Update failed.', [], 500);
        }

        return $this->sendResponse($this->repository->find($id), 'Schedule updated successfully.');
    }

    /**
     * Delete a schedule.
     */
    public function delete(Request $request, int $id)
    {
        $schedule = $this->repository->find($id);
        if (!$schedule) {
            return $this->sendError('Schedule not found.', [], 404);
        }
        
        // Check permission: only admin or schedule owner can delete
        if ($request->user()->role !== 'admin' && $schedule->user_id !== $request->user()->id) {
            return $this->sendError('Unauthorized. You can only delete your own schedules.', [], 403);
        }
        
        $deleted = $this->repository->delete($id);

        if (!$deleted) {
            return $this->sendError('Delete failed.', [], 500);
        }

        return $this->sendResponse([], 'Schedule deleted successfully.');
    }

    public function play(Request $request, int $id)
    {
        $schedule = $this->repository->find($id);
        if (!$schedule) {
            return $this->sendError('Schedule not found.', [], 404);
        }
        
        // Check permission: only admin or schedule owner can play
        if ($request->user()->role !== 'admin' && $schedule->user_id !== $request->user()->id) {
            return $this->sendError('Unauthorized. You can only play your own schedules.', [], 403);
        }
        
        $played = $this->repository->activateSchedule($id);

        if (!$played) {
            return $this->sendError('Play failed.', [], 500);
        }

        return $this->sendResponse([], 'Schedule played successfully.');
    }
}
