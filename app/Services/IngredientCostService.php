<?php

namespace App\Services;

use App\Models\Ingredient;

class IngredientCostService
{
    public function calculateUnitCost(Ingredient $ingredient): float
    {
        if ($ingredient->purchase_quantity == 0) {
            return 0;
        }

        return $ingredient->purchase_price / $ingredient->purchase_quantity;
    }
}