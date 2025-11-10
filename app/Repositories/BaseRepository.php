<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Listing all records with optional filters, sorting, and pagination.
     *
     * @param array $filters
     * @param string|null $sortBy
     * @param string $sortDirection
     * @param int|null $page
     * @param int|null $itemPerPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function listing(
        array $filters = [],
        array $sortBy = [],
        array $sortDirection = [],
        int $page = 1,
        int $itemPerPage = 10,
        array $with = [] // 👈 thêm danh sách các quan hệ cần load
    ) {
        $query = $this->model->newQuery();

        // 🔹 Eager load quan hệ nếu có
        if (!empty($with)) {
            $query->with($with);
        }

        // 🔹 Apply filters
        foreach ($filters as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, 'like', "%{$value}%");
            }
        }

        // 🔹 Apply sorting
        if (!empty($sortBy)) {
            foreach ($sortBy as $index => $column) {
                $direction = $sortDirection[$index] ?? 'asc';
                $query->orderBy($column, $direction);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // 🔹 Pagination hoặc get toàn bộ
        return $itemPerPage > 0
            ? $query->paginate($itemPerPage, ['*'], 'page', $page)
            : $query->get();
    }

    /**
     * Find a record by ID.
     *
     * @param int $id
     * @return Model|null
     */
    public function find(int $id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a new record.
     *
     * @param array $data
     * @return Model
     */
    public function store(array $data)
    {
        try {
            $user_id = auth()->id();
            $data['user_id'] = $user_id;
            $result = $this->model->create($data);
        } catch (\Exception $e) {
            \Log::error('Error storing record', ['error' => $e->getMessage()]);
            throw $e; // rethrow the exception after logging
        }
        return $result;
    }

    /**
     * Update a record by ID.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data)
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }
        try {
            $result = $record->update($data);
        } catch (\Exception $e) {
            \Log::error('Error storing record', ['error' => $e->getMessage()]);
            throw $e; // rethrow the exception after logging
        }
        return $result;
    }

    /**
     * Delete a record by ID.
     *
     * @param int $id
     * @return bool|null
     */
    public function delete(int $id)
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }
        try {
            $result = $record->delete();
        } catch (\Exception $e) {
            \Log::error('Error storing record', ['error' => $e->getMessage()]);
            throw $e; // rethrow the exception after logging
        }
        return $result;
    }
}