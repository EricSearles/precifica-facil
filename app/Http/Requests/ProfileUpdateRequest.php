<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Closure;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => is_string($this->input('email')) ? mb_strtolower(trim($this->input('email'))) : $this->input('email'),
            'company_email' => is_string($this->input('company_email')) ? mb_strtolower(trim($this->input('company_email'))) : $this->input('company_email'),
            'company_phone' => $this->sanitizePhone($this->input('company_phone')),
            'company_mobile_phone' => $this->sanitizePhone($this->input('company_mobile_phone')),
            'company_document' => $this->sanitizeDigits($this->input('company_document')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'company_name' => ['required', 'string', 'max:255'],
            'company_email' => ['required', 'string', 'email', 'max:255'],
            'company_phone' => [
                'nullable',
                'string',
                'max:30',
                $this->phoneRule(false),
            ],
            'company_mobile_phone' => [
                'required',
                'string',
                'max:30',
                $this->phoneRule(true),
            ],
            'company_document' => [
                'required',
                'string',
                'max:20',
                $this->documentRule(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Informe o nome da empresa.',
            'company_email.required' => 'Informe o e-mail da empresa.',
            'company_email.email' => 'Informe um e-mail válido para a empresa.',
            'company_mobile_phone.required' => 'Informe o celular da empresa. Esse campo é obrigatório para cobrança.',
            'company_document.required' => 'Informe o CPF ou CNPJ da empresa para cobrança.',
        ];
    }

    private function phoneRule(bool $mobile): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($mobile): void {
            if ($value === null || $value === '') {
                return;
            }

            $digits = preg_replace('/\D+/', '', (string) $value) ?: '';
            $valid = $mobile
                ? strlen($digits) === 11
                : in_array(strlen($digits), [10, 11], true);

            if (!$valid) {
                $fail($mobile
                    ? 'Informe um celular válido com DDD. Ex.: 11999998888.'
                    : 'Informe um telefone válido com DDD. Ex.: 1133334444.');
            }
        };
    }

    private function sanitizePhone(mixed $value): ?string
    {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        $digits = $this->sanitizeDigits($value) ?? '';

        return $digits !== '' ? $digits : null;
    }

    private function documentRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if ($value === null || $value === '') {
                return;
            }

            $digits = preg_replace('/\D+/', '', (string) $value) ?: '';

            if (!in_array(strlen($digits), [11, 14], true)) {
                $fail('Informe um CPF ou CNPJ válido, somente números ou com pontuação.');
            }
        };
    }

    private function sanitizeDigits(mixed $value): ?string
    {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $value) ?: '';

        return $digits !== '' ? $digits : null;
    }
}
