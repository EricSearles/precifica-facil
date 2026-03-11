<?php

namespace App\Http\Requests\RecipeItems;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRecipeItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'ingredient_id' => ['required', 'integer', Rule::exists('ingredients', 'id')->where(fn ($query) => $query->where('company_id', $this->user()->company_id))],
            'quantity_used' => ['required', 'numeric', 'gt:0'],
            'unit_used' => ['required', 'string', 'max:20'],
        ];
    }
}
