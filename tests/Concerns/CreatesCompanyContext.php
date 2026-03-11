<?php

namespace Tests\Concerns;

use App\Models\Company;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait CreatesCompanyContext
{
    protected function createCompanyContext(array $overrides = []): array
    {
        $company = Company::create([
            'name' => $overrides['company_name'] ?? 'Empresa Teste',
            'slug' => $overrides['company_slug'] ?? Str::slug(($overrides['company_name'] ?? 'Empresa Teste').'-'.Str::random(5)),
            'email' => $overrides['company_email'] ?? 'empresa@example.com',
            'phone' => $overrides['company_phone'] ?? '11999999999',
            'plan' => 'pro',
            'status' => 'active',
        ]);

        $setting = Setting::create([
            'company_id' => $company->id,
            'default_profit_margin' => $overrides['default_profit_margin'] ?? 30,
            'currency' => 'BRL',
            'decimal_places' => $overrides['decimal_places'] ?? 2,
        ]);

        $user = User::create([
            'name' => $overrides['user_name'] ?? 'Usuario Teste',
            'email' => $overrides['user_email'] ?? 'usuario@example.com',
            'password' => Hash::make($overrides['password'] ?? 'password'),
            'company_id' => $company->id,
            'role' => 'owner',
            'is_owner' => true,
        ]);

        return compact('company', 'setting', 'user');
    }
}