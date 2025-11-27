<?php

namespace App\Http\Requests\Note;

class UpdateRequest extends \App\Http\Requests\Base\UpdateRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'note_date' => 'required|date',
            'status' => 'nullable|string',
        ];
    }
}
