<?php

namespace App\Http\Requests\Schedule;

class UpdateRequest extends \App\Http\Requests\Base\UpdateRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'nullable|integer',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string',
            'repeat_type' => 'nullable|string',
            'days_of_week' => 'nullable|string',
            'days_of_month' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'has_fixed_time' => 'nullable|boolean',
            'fixed_time' => 'nullable|string',
            'notify_before_minutes' => 'nullable|integer',
            'notify_times' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'shareable' => 'nullable|boolean',
            'require_checkin' => 'nullable|boolean',
        ];
    }
}
