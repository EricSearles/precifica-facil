<?php

namespace App\Http\Requests\Ingredients;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ingredients', 'name')->where(fn ($query) => $query->where('company_id', $this->user()->company_id)),
            ],
            'brand' => ['nullable', 'string', 'max:255'],
            'purchase_unit' => ['required', 'string', 'max:20'],
            'purchase_quantity' => ['required', 'numeric', 'gt:0'],
            'purchase_price' => ['required', 'numeric', 'gte:0'],
            'base_unit' => ['nullable', 'string', 'max:20'],
            'base_quantity' => ['nullable', 'numeric', 'gt:0'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
