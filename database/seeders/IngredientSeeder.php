<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        Ingredient::insert([
            [
                'company_id' => 1,
                'name' => 'Leite condensado',
                'purchase_unit' => 'un',
                'purchase_quantity' => 1,
                'purchase_price' => 7.50,
                'base_unit' => 'un',
                'base_quantity' => 1,
                'unit_cost' => 7.50
            ],
            [
                'company_id' => 1,
                'name' => 'Chocolate em pó',
                'purchase_unit' => 'g',
                'purchase_quantity' => 1000,
                'purchase_price' => 18.00,
                'base_unit' => 'g',
                'base_quantity' => 1000,
                'unit_cost' => 0.018
            ],
            [
                'company_id' => 1,
                'name' => 'Manteiga',
                'purchase_unit' => 'g',
                'purchase_quantity' => 200,
                'purchase_price' => 8.00,
                'base_unit' => 'g',
                'base_quantity' => 200,
                'unit_cost' => 0.04
            ]
        ]);
    }
}