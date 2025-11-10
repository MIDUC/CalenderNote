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
        ];
    }
}
