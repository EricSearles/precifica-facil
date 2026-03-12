<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Repositories\IngredientRepository;

class IngredientService
{
    public function __construct(
        protected IngredientRepository $ingredientRepository,
        protected IngredientCostService $ingredientCostService,
        protected UnitConversionService $unitConversionService,
    ) {
    }

    public function create(array $data, int $companyId): Ingredient
    {
        $normalizedData = $this->normalizeConversionData($data);

        $ingredient = $this->ingredientRepository->create([
            'company_id' => $companyId,
            'name' => $normalizedData['name'],
            'brand' => $normalizedData['brand'] ?? null,
            'purchase_unit' => $normalizedData['purchase_unit'],
            'purchase_quantity' => $normalizedData['purchase_quantity'],
            'purchase_price' => $normalizedData['purchase_price'],
            'content_quantity' => $normalizedData['content_quantity'] ?? null,
            'content_unit' => $normalizedData['content_unit'] ?? null,
            'base_unit' => $normalizedData['base_unit'] ?? null,
            'base_quantity' => $normalizedData['base_quantity'] ?? null,
            'unit_cost' => 0,
            'notes' => $normalizedData['notes'] ?? null,
            'is_active' => $normalizedData['is_active'] ?? true,
        ]);

        $ingredient->unit_cost = $this->ingredientCostService->calculateUnitCost($ingredient);

        return $this->ingredientRepository->save($ingredient);
    }

    public function update(Ingredient $ingredient, array $data): Ingredient
    {
        $normalizedData = $this->normalizeConversionData($data);

        $ingredient->name = $normalizedData['name'];
        $ingredient->brand = $normalizedData['brand'] ?? null;
        $ingredient->purchase_unit = $normalizedData['purchase_unit'];
        $ingredient->purchase_quantity = $normalizedData['purchase_quantity'];
        $ingredient->purchase_price = $normalizedData['purchase_price'];
        $ingredient->content_quantity = $normalizedData['content_quantity'] ?? null;
        $ingredient->content_unit = $normalizedData['content_unit'] ?? null;
        $ingredient->base_unit = $normalizedData['base_unit'] ?? null;
        $ingredient->base_quantity = $normalizedData['base_quantity'] ?? null;
        $ingredient->notes = $normalizedData['notes'] ?? null;
        $ingredient->is_active = $normalizedData['is_active'] ?? false;
        $ingredient->unit_cost = $this->ingredientCostService->calculateUnitCost($ingredient);

        return $this->ingredientRepository->save($ingredient);
    }

    public function delete(Ingredient $ingredient): void
    {
        $this->ingredientRepository->delete($ingredient);
    }

    protected function normalizeConversionData(array $data): array
    {
        $purchaseUnit = $this->unitConversionService->normalize($data['purchase_unit'] ?? null);
        $contentUnit = $this->unitConversionService->normalize($data['content_unit'] ?? null);
        $baseUnit = $this->unitConversionService->normalize($data['base_unit'] ?? null);
        $purchaseQuantity = isset($data['purchase_quantity']) ? (float) $data['purchase_quantity'] : 0;
        $contentQuantity = isset($data['content_quantity']) && $data['content_quantity'] !== '' ? (float) $data['content_quantity'] : 0;

        if ($contentUnit !== null && $baseUnit !== null && $purchaseQuantity > 0 && $contentQuantity > 0 && $this->unitConversionService->canConvert($contentUnit, $baseUnit)) {
            $data['base_quantity'] = $this->unitConversionService->convert($purchaseQuantity * $contentQuantity, $contentUnit, $baseUnit);
        } elseif ($purchaseUnit !== null && $baseUnit !== null && $purchaseQuantity > 0 && $this->unitConversionService->canConvert($purchaseUnit, $baseUnit)) {
            $data['base_quantity'] = $this->unitConversionService->convert($purchaseQuantity, $purchaseUnit, $baseUnit);
        } else {
            $data['base_quantity'] = null;
        }

        return $data;
    }
}
