<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'company_id' => 1,
            'category_id' => 1,
            'name' => 'Brigadeiro',
            'sale_unit' => 'un',
            'yield_quantity' => 40,
            'profit_margin_type' => 'percentage',
            'profit_margin_value' => 100,
            'use_global_margin' => false,
        ]);
    }
}