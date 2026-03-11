<?php

namespace App\Services;

use App\Models\Recipe;

class RecipeCostService
{
    public function calculateRecipeCost(Recipe $recipe): float
    {
        $total = 0;

        foreach ($recipe->items as $item) {
            $total += $item->total_cost;
        }

        $total += $recipe->extra_cost_total;
        $total += $recipe->packaging_cost_total;

        return $total;
    }

    public function calculateUnitCost(Recipe $recipe): float
    {
        if ($recipe->yield_quantity == 0) {
            return 0;
        }

        return $this->calculateRecipeCost($recipe) / $recipe->yield_quantity;
    }
}