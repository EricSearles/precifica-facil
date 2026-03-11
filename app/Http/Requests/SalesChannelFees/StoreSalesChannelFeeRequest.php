<?php

namespace App\Http\Requests\SalesChannelFees;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSalesChannelFeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'sales_channel_id' => [
                'required',
                'integer',
                Rule::exists('sales_channels', 'id')->where(fn ($query) => $query->where('company_id', $this->user()->company_id)),
            ],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['fixed', 'percentage'])],
            'value' => ['required', 'numeric', 'gte:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}