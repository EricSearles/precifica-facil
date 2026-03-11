<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where(fn ($query) => $query->where('company_id', $this->user()->company_id))],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')
                    ->where(fn ($query) => $query->where('company_id', $this->user()->company_id))
                    ->ignore($productId),
            ],
            'sale_unit' => ['required', 'string', 'max:20'],
            'yield_quantity' => ['required', 'numeric', 'gt:0'],
            'profit_margin_type' => ['required', Rule::in(['percentage', 'fixed'])],
            'profit_margin_value' => ['required', 'numeric', 'gte:0'],
            'use_global_margin' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
