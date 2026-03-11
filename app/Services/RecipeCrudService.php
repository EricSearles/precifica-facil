<?php

namespace App\Services;

use App\Models\Recipe;
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
}
