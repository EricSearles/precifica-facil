<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::create([
            'id' => 1,
            'name' => 'Doces da Maria',
            'slug' => 'doces-da-maria',
            'email' => 'contato@docesdamaria.com',
            'phone' => '11999999999',
            'plan' => 'free',
            'status' => 'active',
        ]);
    }
}