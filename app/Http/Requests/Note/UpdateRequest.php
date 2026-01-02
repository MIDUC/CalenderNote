<?php

namespace App\Http\Requests\Note;

class UpdateRequest extends \App\Http\Requests\Base\UpdateRequest
{
    public function authorize(): bool
    {
        return true; // Nếu có middleware auth, có thể check quyền ở đây
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
        ];
    }
}

