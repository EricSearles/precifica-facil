<?php

namespace App\Services;

use App\Models\ExtraCost;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Repositories\RecipeRepository;

class RecipeCrudService
{
    public function __construct(
        protected RecipeRepository $recipeRepository,
    ) {
    }

    public function create(array $data, int $companyId): Recipe
    {
        return $this->recipeRepository->create([
            'company_id' => $companyId,
            'product_id' => $data['product_id'],
            'name' => $data['name'],
            'yield_quantity' => $data['yield_quantity'],
            'yield_unit' => $data['yield_unit'],
            'ingredients_cost_total' => 0,
            'extra_cost_total' => 0,
            'packaging_cost_total' => 0,
            'recipe_total_cost' => 0,
            'unit_cost' => 0,
            'suggested_sale_price' => 0,
            'preparation_method' => $data['preparation_method'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function update(Recipe $recipe, array $data): Recipe
    {
        $recipe->product_id = $data['product_id'];
        $recipe->name = $data['name'];
        $recipe->yield_quantity = $data['yield_quantity'];
        $recipe->yield_unit = $data['yield_unit'];
        $recipe->preparation_method = $data['preparation_method'] ?? null;
        $recipe->notes = $data['notes'] ?? null;

        return $this->recipeRepository->save($recipe);
    }

    public function delete(Recipe $recipe): void
    {
        $this->recipeRepository->delete($recipe);
    }

    public function duplicate(Recipe $recipe): Recipe
    {
        $recipe->loadMissing('items', 'extraCosts');

        $duplicate = $this->recipeRepository->create([
            'company_id' => $recipe->company_id,
            'product_id' => $recipe->product_id,
            'name' => $this->duplicateName($recipe->name),
            'yield_quantity' => $recipe->yield_quantity,
            'yield_unit' => $recipe->yield_unit,
            'ingredients_cost_total' => $recipe->ingredients_cost_total,
            'extra_cost_total' => $recipe->extra_cost_total,
            'packaging_cost_total' => $recipe->packaging_cost_total,
            'recipe_total_cost' => $recipe->recipe_total_cost,
            'unit_cost' => $recipe->unit_cost,
            'suggested_sale_price' => $recipe->suggested_sale_price,
            'preparation_method' => $recipe->preparation_method,
            'notes' => $recipe->notes,
        ]);

        foreach ($recipe->items as $item) {
            RecipeItem::query()->create([
                'company_id' => $duplicate->company_id,
                'recipe_id' => $duplicate->id,
                'ingredient_id' => $item->ingredient_id,
                'quantity_used' => $item->quantity_used,
                'unit_used' => $item->unit_used,
                'unit_cost_snapshot' => $item->unit_cost_snapshot,
                'total_cost' => $item->total_cost,
            ]);
        }

        foreach ($recipe->extraCosts as $extraCost) {
            ExtraCost::query()->create([
                'company_id' => $duplicate->company_id,
                'product_id' => $duplicate->product_id,
                'recipe_id' => $duplicate->id,
                'description' => $extraCost->description,
                'type' => $extraCost->type,
                'value' => $extraCost->value,
                'labor_minutes' => $extraCost->labor_minutes,
                'labor_hourly_rate' => $extraCost->labor_hourly_rate,
                'monthly_salary' => $extraCost->monthly_salary,
                'monthly_hours' => $extraCost->monthly_hours,
            ]);
        }

        return $duplicate->fresh();
    }

    private function duplicateName(string $name): string
    {
        return str($name)->finish('')->append(' (Cópia)')->toString();
    }
}
