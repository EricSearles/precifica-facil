<?php

namespace App\Services;

use App\Models\ExtraCost;
use App\Repositories\ExtraCostRepository;

class ExtraCostService
{
    public function __construct(
        protected ExtraCostRepository $extraCostRepository,
        protected RecipeService $recipeService,
    ) {
    }

    public function create(array $data, int $companyId): ExtraCost
    {
        $extraCost = $this->extraCostRepository->create([
            'company_id' => $companyId,
            'product_id' => null,
            'recipe_id' => $data['recipe_id'],
            'description' => $data['description'],
            'type' => $data['type'],
            'value' => $data['value'],
        ]);

        $this->recipeService->recalculateAndUpdate((int) $extraCost->recipe_id, $companyId);

        return $extraCost;
    }

    public function update(ExtraCost $extraCost, array $data, int $companyId): ExtraCost
    {
        $extraCost->description = $data['description'];
        $extraCost->type = $data['type'];
        $extraCost->value = $data['value'];

        $this->extraCostRepository->save($extraCost);
        $this->recipeService->recalculateAndUpdate((int) $extraCost->recipe_id, $companyId);

        return $extraCost;
    }

    public function delete(ExtraCost $extraCost, int $companyId): void
    {
        $recipeId = (int) $extraCost->recipe_id;

        $this->extraCostRepository->delete($extraCost);
        $this->recipeService->recalculateAndUpdate($recipeId, $companyId);
    }
}
