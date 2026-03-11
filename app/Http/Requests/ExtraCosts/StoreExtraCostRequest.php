<?php

namespace App\Http\Requests\ExtraCosts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExtraCostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'recipe_id' => ['required', 'integer', Rule::exists('recipes', 'id')->where(fn ($query) => $query->where('company_id', $this->user()->company_id))],
            'description' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['fixed', 'percentage'])],
            'value' => ['required', 'numeric', 'gte:0'],
        ];
    }
}
