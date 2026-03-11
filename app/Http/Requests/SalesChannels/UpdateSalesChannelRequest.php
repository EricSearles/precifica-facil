<?php

namespace App\Http\Requests\SalesChannels;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSalesChannelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}