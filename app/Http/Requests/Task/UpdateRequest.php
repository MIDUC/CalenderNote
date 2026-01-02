<?php

namespace App\Http\Requests\Task;

class UpdateRequest extends \App\Http\Requests\Base\UpdateRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|string|in:pending,done,failed,blocked',
            'completed_at' => 'sometimes|nullable|date',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'task_date' => 'sometimes|date',
            'fixed_time' => 'sometimes|nullable|string',
        ];
    }
}
