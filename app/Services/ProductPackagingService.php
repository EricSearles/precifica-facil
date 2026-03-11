<?php

namespace App\Services;

use App\Models\ProductPackaging;
use App\Repositories\ProductPackagingRepository;
use App\Repositories\RecipeRepository;

class ProductPackagingService
{
    public function __construct(
        protected ProductPackagingRepository $productPackagingRepository,
        protected RecipeRepository $recipeRepository,
        protected RecipeService $recipeService,
    ) {
    }

    public function create(array $data, int $companyId): ProductPackaging
    {
        $productPackaging = $this->productPackagingRepository->create([
            'company_id' => $companyId,
            'product_id' => $data['product_id'],
            'packaging_id' => $data['packaging_id'],
            'quantity' => $data['quantity'],
            'total_cost' => 0,
        ]);

        $productPackaging->load('packaging');
        $productPackaging->total_cost = (float) $productPackaging->quantity * (float) $productPackaging->packaging?->unit_cost;
        $this->productPackagingRepository->save($productPackaging);

        $this->recalculateRecipesForProduct((int) $productPackaging->product_id, $companyId);

        return $productPackaging;
    }

    public function update(ProductPackaging $productPackaging, array $data, int $companyId): ProductPackaging
    {
        $productPackaging->packaging_id = $data['packaging_id'];
        $productPackaging->quantity = $data['quantity'];
        $productPackaging->load('packaging');
        $productPackaging->total_cost = (float) $productPackaging->quantity * (float) $productPackaging->packaging?->unit_cost;

        $this->productPackagingRepository->save($productPackaging);
        $this->recalculateRecipesForProduct((int) $productPackaging->product_id, $companyId);

        return $productPackaging;
    }

    public function delete(ProductPackaging $productPackaging, int $companyId): void
    {
        $productId = (int) $productPackaging->product_id;

        $this->productPackagingRepository->delete($productPackaging);
        $this->recalculateRecipesForProduct($productId, $companyId);
    }

    protected function recalculateRecipesForProduct(int $productId, int $companyId): void
    {
        $recipes = $this->recipeRepository->getByProduct($productId, $companyId);

        foreach ($recipes as $recipe) {
            $this->recipeService->recalculateAndUpdate((int) $recipe->id, $companyId);
        }
    }
}
