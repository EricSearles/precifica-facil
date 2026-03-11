<?php

namespace App\Http\Requests\Packagings;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackagingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'unit_cost' => ['required', 'numeric', 'gte:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
