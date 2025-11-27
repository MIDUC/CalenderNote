<?php

namespace App\Http\Controllers;

use App\Http\Requests\Note\StoreRequest;
use App\Http\Requests\Note\UpdateRequest;
use App\Repositories\NoteRepository;
use Illuminate\Http\Request;
// use App\Http\Requests\Note\StoreRequest;
// use App\Http\Requests\Note\UpdateRequest;

class NoteController extends BaseController
{
    protected NoteRepository $repository;
    public function __construct(NoteRepository $repository) // dùng thủ công, khởi tạo trong __construct()
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of Notes.
     */
    public function listing(Request $request)
    {
        $filters = $this->filterParams($request);

        $data = $this->repository->listing($filters['filters'], $filters['sort_by'], $filters['sort_direction'], $filters['page'], $filters['item_per_page'], ['user']); // 👈 truyền mảng quan hệ cần load

        return $this->sendResponse($data, 'Notes retrieved successfully.');
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $Note = $this->repository->store($data);

        return $this->sendResponse($Note, 'Note created successfully.');
    }

    /**
     * Update an existing Note.
     */
    public function update(UpdateRequest $request, int $id)
    {
        $updated = $this->repository->update($id, $request->validated());

        if (!$updated) {
            return $this->sendError('Note not found or update failed.', [], 404);
        }

        return $this->sendResponse($this->repository->find($id), 'Note updated successfully.');
    }

    /**
     * Delete a Note.
     */
    public function delete(int $id)
    {
        $deleted = $this->repository->delete($id);

        if (!$deleted) {
            return $this->sendError('Note not found or delete failed.', [], 404);
        }

        return $this->sendResponse([], 'Note deleted successfully.');
    }
}
