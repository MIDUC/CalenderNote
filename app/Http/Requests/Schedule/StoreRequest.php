<?php

namespace App\Http\Requests\Schedule;


class StoreRequest extends \App\Http\Requests\Base\StoreRequest
{
    public function authorize(): bool
    {
        return true; // Nếu có middleware auth, có thể check quyền ở đây
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'repeat_type' => 'nullable|string',
            'days_of_week' => 'nullable|string',
            'days_of_month' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'has_fixed_time' => 'required|boolean',
            'fixed_time' => 'nullable|string',
            'notify_before_minutes' => 'nullable|integer',
            'notify_times' => 'nullable|integer',
            'is_active' => 'required|boolean',
            'shareable' => 'nullable|boolean',
            'require_checkin' => 'nullable|boolean',
        ];
    }
}
