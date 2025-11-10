<?php

namespace App\Http\Controllers;

use App\Repositories\TaskRepository;
use Illuminate\Http\Request;
use App\Http\Requests\Task\StoreRequest;
use App\Http\Requests\Task\UpdateRequest;

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

        $data = $this->repository->listing($filters['filters'], $filters['sort_by'], $filters['sort_direction'], $filters['page'], $filters['item_per_page'],['schedule','user']); // 👈 truyền mảng quan hệ cần load

        return $this->sendResponse($data, 'Tasks retrieved successfully.');
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $Task = $this->repository->store($data);

        return $this->sendResponse($Task, 'Task created successfully.');
    }

    /**
     * Update an existing Task.
     */
    public function update(UpdateRequest $request, int $id)
    {
        $updated = $this->repository->update($id, $request->validated());

        if (!$updated) {
            return $this->sendError('Task not found or update failed.', [], 404);
        }

        return $this->sendResponse($this->repository->find($id), 'Task updated successfully.');
    }

    /**
     * Delete a Task.
     */
    public function delete(int $id)
    {
        $deleted = $this->repository->delete($id);

        if (!$deleted) {
            return $this->sendError('Task not found or delete failed.', [], 404);
        }

        return $this->sendResponse([], 'Task deleted successfully.');
    }
}
