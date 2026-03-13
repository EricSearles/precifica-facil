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
            'value' => ['nullable', 'numeric', 'gte:0'],
            'labor_minutes' => ['nullable', 'integer', 'gt:0'],
            'labor_hourly_rate' => ['nullable', 'numeric', 'gt:0'],
            'monthly_salary' => ['nullable', 'numeric', 'gt:0'],
            'monthly_hours' => ['nullable', 'integer', 'gt:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasLaborMinutes = $this->filled('labor_minutes');
            $hasLaborHourlyRate = $this->filled('labor_hourly_rate');
            $hasMonthlySalary = $this->filled('monthly_salary');
            $hasMonthlyHours = $this->filled('monthly_hours');
            $hasValue = $this->filled('value');

            if ($hasMonthlyHours && ! $hasMonthlySalary) {
                $validator->errors()->add('monthly_salary', 'Informe o salário mensal para usar horas por mês.');
            }

            if ($hasLaborMinutes && ! $hasLaborHourlyRate && ! $hasMonthlySalary) {
                $validator->errors()->add('labor_minutes', 'Preencha tempo e valor por hora ou salário mensal para calcular mão de obra.');
            }

            if (! $hasLaborMinutes && ($hasLaborHourlyRate || $hasMonthlySalary)) {
                $validator->errors()->add('labor_minutes', 'Preencha o tempo de mão de obra para calcular esse custo.');
            }

            if (! $hasValue && ! ($hasLaborMinutes && ($hasLaborHourlyRate || $hasMonthlySalary))) {
                $validator->errors()->add('value', 'Informe um valor manual ou os campos de mão de obra.');
            }
        });
    }
}
