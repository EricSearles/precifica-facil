<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Repositories\IngredientRepository;

class IngredientService
{
    public function __construct(
        protected IngredientRepository $ingredientRepository,
        protected IngredientCostService $ingredientCostService,
    ) {
    }

    public function create(array $data, int $companyId): Ingredient
    {
        $ingredient = $this->ingredientRepository->create([
            'company_id' => $companyId,
            'name' => $data['name'],
            'brand' => $data['brand'] ?? null,
            'purchase_unit' => $data['purchase_unit'],
            'purchase_quantity' => $data['purchase_quantity'],
            'purchase_price' => $data['purchase_price'],
            'base_unit' => $data['base_unit'] ?? null,
            'base_quantity' => $data['base_quantity'] ?? null,
            'unit_cost' => 0,
            'notes' => $data['notes'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        $ingredient->unit_cost = $this->ingredientCostService->calculateUnitCost($ingredient);

        return $this->ingredientRepository->save($ingredient);
    }

    public function update(Ingredient $ingredient, array $data): Ingredient
    {
        $ingredient->name = $data['name'];
        $ingredient->brand = $data['brand'] ?? null;
        $ingredient->purchase_unit = $data['purchase_unit'];
        $ingredient->purchase_quantity = $data['purchase_quantity'];
        $ingredient->purchase_price = $data['purchase_price'];
        $ingredient->base_unit = $data['base_unit'] ?? null;
        $ingredient->base_quantity = $data['base_quantity'] ?? null;
        $ingredient->notes = $data['notes'] ?? null;
        $ingredient->is_active = $data['is_active'] ?? false;
        $ingredient->unit_cost = $this->ingredientCostService->calculateUnitCost($ingredient);

        return $this->ingredientRepository->save($ingredient);
    }

    public function delete(Ingredient $ingredient): void
    {
        $this->ingredientRepository->delete($ingredient);
    }
}
