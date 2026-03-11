<?php

namespace App\Services;

use App\Models\RecipeItem;
use App\Repositories\RecipeItemRepository;

class RecipeItemService
{
    public function __construct(
        protected RecipeItemRepository $recipeItemRepository,
        protected RecipeService $recipeService,
    ) {
    }

    public function create(array $data, int $companyId): RecipeItem
    {
        $recipeItem = $this->recipeItemRepository->create([
            'company_id' => $companyId,
            'recipe_id' => $data['recipe_id'],
            'ingredient_id' => $data['ingredient_id'],
            'quantity_used' => $data['quantity_used'],
            'unit_used' => $data['unit_used'],
            'unit_cost_snapshot' => 0,
            'total_cost' => 0,
        ]);

        $this->recipeService->recalculateAndUpdate((int) $recipeItem->recipe_id, $companyId);

        return $recipeItem->fresh(['ingredient', 'recipe']);
    }

    public function update(RecipeItem $recipeItem, array $data, int $companyId): RecipeItem
    {
        $recipeItem->ingredient_id = $data['ingredient_id'];
        $recipeItem->quantity_used = $data['quantity_used'];
        $recipeItem->unit_used = $data['unit_used'];

        $this->recipeItemRepository->save($recipeItem);
        $this->recipeService->recalculateAndUpdate((int) $recipeItem->recipe_id, $companyId);

        return $recipeItem->fresh(['ingredient', 'recipe']);
    }

    public function delete(RecipeItem $recipeItem, int $companyId): void
    {
        $recipeId = (int) $recipeItem->recipe_id;

        $this->recipeItemRepository->delete($recipeItem);
        $this->recipeService->recalculateAndUpdate($recipeId, $companyId);
    }
}
