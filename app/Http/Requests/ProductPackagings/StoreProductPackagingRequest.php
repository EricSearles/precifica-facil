<?php

namespace App\Http\Requests\ProductPackagings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductPackagingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')->where(fn ($query) => $query->where('company_id', $this->user()->company_id))],
            'packaging_id' => ['required', 'integer', Rule::exists('packagings', 'id')->where(fn ($query) => $query->where('company_id', $this->user()->company_id))],
            'quantity' => ['required', 'numeric', 'gt:0'],
        ];
    }
}
