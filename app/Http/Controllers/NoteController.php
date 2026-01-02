<?php

namespace App\Http\Controllers;

use App\Repositories\NoteRepository;
use Illuminate\Http\Request;
use App\Http\Requests\Note\StoreRequest;
use App\Http\Requests\Note\UpdateRequest;

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
        
        // If not admin, only show current user's notes
        if ($request->user()->role !== 'admin') {
            $filters['filters']['user_id'] = $request->user()->id;
        }

        $data = $this->repository->listing($filters['filters'], $filters['sort_by'], $filters['sort_direction'], $filters['page'], $filters['item_per_page'], ['user']); // 👈 truyền mảng quan hệ cần load

        return $this->sendResponse($data, 'Notes retrieved successfully.');
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        
        // Tự động set note_date nếu không có
        if (!isset($data['note_date']) || empty($data['note_date'])) {
            $data['note_date'] = now()->format('Y-m-d');
        }
        
        // Tự động set user_id từ authenticated user
        if (!isset($data['user_id'])) {
            $data['user_id'] = $request->user()->id;
        }
        
        // Set status mặc định nếu không có
        if (!isset($data['status'])) {
            $data['status'] = 'new';
        }
        
        $Note = $this->repository->store($data);

        return $this->sendResponse($Note, 'Note created successfully.');
    }

    /**
     * Display a single Note.
     */
    public function show(Request $request, int $id)
    {
        $note = $this->repository->find($id);
        if (!$note) {
            return $this->sendError('Note not found.', [], 404);
        }
        
        // Check permission: only admin or note owner can view
        if ($request->user()->role !== 'admin' && $note->user_id !== $request->user()->id) {
            return $this->sendError('Unauthorized. You can only view your own notes.', [], 403);
        }
        
        return $this->sendResponse($note->load('user'), 'Note retrieved successfully.');
    }

    /**
     * Update an existing Note.
     */
    public function update(UpdateRequest $request, int $id)
    {
        $note = $this->repository->find($id);
        if (!$note) {
            return $this->sendError('Note not found.', [], 404);
        }
        
        // Check permission: only admin or note owner can update
        if ($request->user()->role !== 'admin' && $note->user_id !== $request->user()->id) {
            return $this->sendError('Unauthorized. You can only update your own notes.', [], 403);
        }
        
        $updated = $this->repository->update($id, $request->validated());

        if (!$updated) {
            return $this->sendError('Update failed.', [], 500);
        }

        return $this->sendResponse($this->repository->find($id), 'Note updated successfully.');
    }

    /**
     * Delete a Note.
     */
    public function delete(Request $request, int $id)
    {
        $note = $this->repository->find($id);
        if (!$note) {
            return $this->sendError('Note not found.', [], 404);
        }
        
        // Check permission: only admin or note owner can delete
        if ($request->user()->role !== 'admin' && $note->user_id !== $request->user()->id) {
            return $this->sendError('Unauthorized. You can only delete your own notes.', [], 403);
        }
        
        $deleted = $this->repository->delete($id);

        if (!$deleted) {
            return $this->sendError('Delete failed.', [], 500);
        }

        return $this->sendResponse([], 'Note deleted successfully.');
    }
}
