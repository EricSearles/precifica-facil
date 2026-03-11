<?php

namespace App\Http\Requests\ProductChannelPrices;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductChannelPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'desired_net_value' => ['nullable', 'numeric', 'gte:0'],
        ];
    }
}