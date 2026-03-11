<?php

namespace App\Repositories;

use App\Models\Setting;

class SettingRepository
{
    public function create(array $data): Setting
    {
        return Setting::create($data);
    }

    public function firstOrCreateByCompany(int $companyId, array $defaults = []): Setting
    {
        return Setting::firstOrCreate(
            ['company_id' => $companyId],
            $defaults,
        );
    }

    public function findByCompanyId(int $companyId): ?Setting
    {
        return Setting::where('company_id', $companyId)->first();
    }

    public function save(Setting $setting): Setting
    {
        $setting->save();

        return $setting;
    }
}
