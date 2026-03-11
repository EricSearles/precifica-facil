<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'company_id' => 1,
            'default_profit_margin' => 100,
            'currency' => 'BRL',
            'decimal_places' => 2
        ]);
    }
}