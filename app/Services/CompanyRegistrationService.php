<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\CompanyRepository;
use App\Repositories\SettingRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanyRegistrationService
{
    public function __construct(
        protected CompanyRepository $companyRepository,
        protected UserRepository $userRepository,
        protected SettingRepository $settingRepository,
    ) {
    }

    public function registerOwner(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $company = $this->companyRepository->create([
                'name' => $data['company_name'],
                'slug' => $this->generateUniqueSlug($data['company_name']),
                'email' => $data['email'],
                'phone' => $data['company_phone'] ?? null,
                'plan' => 'trial',
                'status' => 'active',
                'trial_ends_at' => now()->addDays(14),
            ]);

            $user = $this->userRepository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'company_id' => $company->id,
                'role' => 'owner',
                'is_owner' => true,
            ]);

            $this->settingRepository->create([
                'company_id' => $company->id,
                'default_profit_margin' => 0,
                'currency' => 'BRL',
                'decimal_places' => 2,
            ]);

            return $user;
        });
    }

    protected function generateUniqueSlug(string $companyName): string
    {
        $baseSlug = Str::slug($companyName);
        $rootSlug = $baseSlug !== '' ? $baseSlug : 'empresa';
        $slug = $rootSlug;
        $counter = 1;

        while ($this->companyRepository->existsBySlug($slug)) {
            $slug = $rootSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
