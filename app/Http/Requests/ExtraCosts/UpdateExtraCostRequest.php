<?php

namespace App\Http\Requests\ExtraCosts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExtraCostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['fixed', 'percentage'])],
            'value' => ['nullable', 'numeric', 'gte:0'],
            'labor_minutes' => ['nullable', 'integer', 'gt:0'],
            'labor_hourly_rate' => ['nullable', 'numeric', 'gt:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasLaborMinutes = $this->filled('labor_minutes');
            $hasLaborHourlyRate = $this->filled('labor_hourly_rate');
            $hasValue = $this->filled('value');

            if ($hasLaborMinutes xor $hasLaborHourlyRate) {
                $validator->errors()->add('labor_minutes', 'Preencha tempo e valor por hora para calcular mão de obra.');
            }

            if (! $hasValue && ! ($hasLaborMinutes && $hasLaborHourlyRate)) {
                $validator->errors()->add('value', 'Informe um valor manual ou os campos de mão de obra.');
            }
        });
    }
}
