<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RecipeItem;

class RecipeItemSeeder extends Seeder
{
    public function run(): void
    {
        RecipeItem::insert([
            [
                'company_id' => 1,
                'recipe_id' => 1,
                'ingredient_id' => 1,
                'quantity_used' => 1,
                'unit_used' => 'un',
                'unit_cost_snapshot' => 7.50,
                'total_cost' => 7.50
            ],
            [
                'company_id' => 1,
                'recipe_id' => 1,
                'ingredient_id' => 2,
                'quantity_used' => 30,
                'unit_used' => 'g',
                'unit_cost_snapshot' => 0.018,
                'total_cost' => 0.54
            ],
            [
                'company_id' => 1,
                'recipe_id' => 1,
                'ingredient_id' => 3,
                'quantity_used' => 20,
                'unit_used' => 'g',
                'unit_cost_snapshot' => 0.04,
                'total_cost' => 0.80
            ]
        ]);
    }
}