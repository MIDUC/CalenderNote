<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
        Log::debug('Listing Query', ['filters' => $filters, 'sortBy' => $sortBy, 'sortDirection' => $sortDirection, 'page' => $page, 'itemPerPage' => $itemPerPage]);
        // 🔹 Apply filters
        $dateFields = ['task_date', 'start_date', 'end_date', 'created_at'];

        foreach ($filters as $field => $value) {
            Log::debug('Applying filter', ['field' => $field, 'value' => $value]);

            if ($value === null || $value === '') {
                continue;
            }

            // 1. Lọc cho Mảng (WHERE IN)
            if (is_array($value)) {
                $query->whereIn($field, $value);
            }

            // 2. ⭐ Lọc cho Ngày tháng (WHERE DATE) - Dùng danh sách đã xác định
            else if (in_array($field, $dateFields)) {
                // Áp dụng whereDate cho các trường trong danh sách $dateFields
                // Giả sử tên cột trong DB trùng với tên trường ($field)
                $query->whereDate($field, $value);
            }

            // 3. Lọc Giá trị đơn còn lại (WHERE)
            else {
                // Áp dụng lọc chính xác cho các trường còn lại (status, title, v.v.)
                $query->where($field, $value);
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
            \Log::warning('Record not found for update', ['id' => $id, 'model' => get_class($this->model)]);
            return false;
        }
        
        \Log::info('Updating record', [
            'id' => $id,
            'model' => get_class($this->model),
            'data' => $data,
            'current_status' => $record->status ?? 'N/A',
        ]);
        
        try {
            $result = $record->update($data);
            \Log::info('Update result', [
                'id' => $id,
                'success' => $result,
                'updated_status' => $record->fresh()->status ?? 'N/A',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating record', [
                'id' => $id,
                'model' => get_class($this->model),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
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