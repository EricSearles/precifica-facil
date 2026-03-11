<?php

namespace App\Services;

use App\Models\Setting;
use App\Repositories\SettingRepository;

class SettingService
{
    public function __construct(
        protected SettingRepository $settingRepository,
    ) {
    }

    public function getOrCreateForCompany(int $companyId): Setting
    {
        return $this->settingRepository->firstOrCreateByCompany($companyId, [
            'default_profit_margin' => 0,
            'currency' => 'BRL',
            'decimal_places' => 2,
        ]);
    }

    public function updateForCompany(int $companyId, array $data): Setting
    {
        $setting = $this->getOrCreateForCompany($companyId);

        $setting->default_profit_margin = $data['default_profit_margin'];
        $setting->currency = $data['currency'];
        $setting->decimal_places = $data['decimal_places'];

        return $this->settingRepository->save($setting);
    }
}
