<?php

namespace App\Repositories;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Collection;

class RecipeRepository
{
    public function getByCompany(int $companyId): Collection
    {
        return Recipe::with(['company.setting', 'product.productChannelPrices.salesChannel', 'product'])
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get();
    }

    public function getByProduct(int $productId, int $companyId): Collection
    {
        return Recipe::where('company_id', $companyId)
            ->where('product_id', $productId)
            ->get();
    }

    public function findById(int $id, int $companyId): ?Recipe
    {
        return Recipe::with(['company.setting', 'product.productChannelPrices.salesChannel', 'product'])
            ->where('company_id', $companyId)
            ->where('id', $id)
            ->first();
    }

    public function findWithItems(int $id, int $companyId): ?Recipe
    {
        return Recipe::with(['company.setting', 'product.productChannelPrices.salesChannel', 'product.productPackagings.packaging', 'product', 'items.ingredient', 'extraCosts'])
            ->where('company_id', $companyId)
            ->where('id', $id)
            ->first();
    }

    public function create(array $data): Recipe
    {
        return Recipe::create($data);
    }

    public function save(Recipe $recipe): Recipe
    {
        $recipe->save();

        return $recipe;
    }

    public function delete(Recipe $recipe): void
    {
        $recipe->delete();
    }
}