<?php

namespace App\Http\Requests\Note;


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
            'content' => 'nullable|string',
            'note_date' => 'required|date',
        ];
    }
}
