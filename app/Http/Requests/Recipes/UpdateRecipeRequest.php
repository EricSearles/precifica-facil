<?php

namespace App\Http\Requests\Recipes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')->where(fn ($query) => $query->where('company_id', $this->user()->company_id))],
            'name' => ['required', 'string', 'max:255'],
            'yield_quantity' => ['required', 'numeric', 'gt:0'],
            'yield_unit' => ['required', 'string', 'max:20'],
            'preparation_method' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
