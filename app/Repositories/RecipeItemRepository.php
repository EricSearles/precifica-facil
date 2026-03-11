<?php

namespace App\Repositories;

use App\Models\RecipeItem;
use Illuminate\Database\Eloquent\Collection;

class RecipeItemRepository
{
    public function getByRecipe(int $recipeId, int $companyId): Collection
    {
        return RecipeItem::with('ingredient')
            ->where('company_id', $companyId)
            ->where('recipe_id', $recipeId)
            ->orderBy('id')
            ->get();
    }

    public function findById(int $id, int $companyId): ?RecipeItem
    {
        return RecipeItem::with('ingredient', 'recipe')
            ->where('company_id', $companyId)
            ->where('id', $id)
            ->first();
    }

    public function create(array $data): RecipeItem
    {
        return RecipeItem::create($data);
    }

    public function save(RecipeItem $recipeItem): RecipeItem
    {
        $recipeItem->save();

        return $recipeItem;
    }

    public function delete(RecipeItem $recipeItem): void
    {
        $recipeItem->delete();
    }
}
