<?php

namespace App\Support;

use App\Models\Company;
use App\Models\Setting;

class CompanyFormatter
{
    public function decimalPlaces(?Company $company = null): int
    {
        $places = $company?->setting?->decimal_places;

        return is_numeric($places) ? max(0, min(4, (int) $places)) : 2;
    }

    public function roundMoney(float $value, ?Company $company = null): float
    {
        return round($value, $this->decimalPlaces($company));
    }

    public function money(float|int|string|null $value, ?Company $company = null): string
    {
        $formatted = number_format((float) $value, $this->decimalPlaces($company), ',', '.');
        $currency = strtoupper((string) ($company?->setting?->currency ?? 'BRL'));

        return match ($currency) {
            'BRL' => 'R$ '.$formatted,
            default => $formatted,
        };
    }

    public function fromSetting(?Setting $setting, float $value): float
    {
        $places = is_numeric($setting?->decimal_places) ? max(0, min(4, (int) $setting->decimal_places)) : 2;

        return round($value, $places);
    }
}
