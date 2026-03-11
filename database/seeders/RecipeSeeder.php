<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        Recipe::create([
            'company_id' => 1,
            'product_id' => 1,
            'name' => 'Brigadeiro tradicional',
            'yield_quantity' => 40,
            'yield_unit' => 'un'
        ]);
    }
}