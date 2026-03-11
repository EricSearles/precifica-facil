<?php

namespace App\Http\Requests\ProductChannelPrices;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductChannelPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where(fn ($query) => $query->where('company_id', $this->user()->company_id)),
            ],
            'sales_channel_id' => [
                'required',
                'integer',
                Rule::exists('sales_channels', 'id')->where(fn ($query) => $query->where('company_id', $this->user()->company_id)),
            ],
            'desired_net_value' => ['nullable', 'numeric', 'gte:0'],
        ];
    }
}