<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::insert([
            ['company_id' => 1, 'name' => 'Doces'],
            ['company_id' => 1, 'name' => 'Salgados'],
            ['company_id' => 1, 'name' => 'Bolos'],
        ]);
    }
}