<?php

namespace App\Http\Requests\Base;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Nếu có middleware auth, có thể check quyền ở đây
    }

    public function rules(): array
    {
        return [
        ];
    }
}
