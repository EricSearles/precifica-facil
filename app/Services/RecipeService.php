<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Recipe;
use App\Repositories\ProductRepository;
use App\Repositories\RecipeRepository;
use App\Support\CompanyFormatter;
use Illuminate\Support\Facades\DB;

class RecipeService
{
    public function __construct(
        protected RecipeRepository $recipeRepository,
        protected ProductRepository $productRepository,
        protected IngredientCostService $ingredientCostService,
        protected RecipeCostService $recipeCostService,
        protected ProductPricingService $productPricingService,
        protected ProductChannelPriceService $productChannelPriceService,
        protected CompanyFormatter $companyFormatter,
    ) {
    }

    public function recalculateAndUpdate(int $recipeId, int $companyId): ?Recipe
    {
        $recipe = $this->recipeRepository->findWithItems($recipeId, $companyId);

        if (! $recipe) {
            return null;
        }

        $recipe->loadMissing('company.setting');

        return DB::transaction(function () use ($recipe, $companyId) {
            $this->recalculateItemCosts($recipe);
            $this->recalculateExtraCosts($recipe);
            $this->recalculatePackagingCosts($recipe);
            $this->recalculateRecipeTotals($recipe);

            $product = $this->productRepository->findById($recipe->product_id, $companyId);

            if ($product) {
                $this->updateProductPricing($recipe, $product, $companyId);
            }

            return $recipe->load(['company.setting', 'product.productPackagings.packaging', 'product.company.setting', 'product', 'items.ingredient', 'extraCosts']);
        });
    }

    protected function recalculateItemCosts(Recipe $recipe): void
    {
        $ingredientsCostTotal = 0;

        foreach ($recipe->items as $item) {
            $ingredient = $item->ingredient;
            $costBreakdown = $ingredient
                ? $this->ingredientCostService->calculateCostForUsage(
                    $ingredient,
                    (float) $item->quantity_used,
                    (string) $item->unit_used,
                )
                : ['unit_cost' => 0.0, 'total_cost' => 0.0];

            $item->unit_cost_snapshot = $this->companyFormatter->roundMoney(
                (float) $costBreakdown['unit_cost'],
                $recipe->company,
            );
            $item->total_cost = $this->companyFormatter->roundMoney((float) $costBreakdown['total_cost'], $recipe->company);
            $item->save();

            $ingredientsCostTotal += (float) $item->total_cost;
        }

        $recipe->ingredients_cost_total = $this->companyFormatter->roundMoney($ingredientsCostTotal, $recipe->company);
    }

    protected function recalculateExtraCosts(Recipe $recipe): void
    {
        $extraCostTotal = 0;

        foreach ($recipe->extraCosts as $extraCost) {
            if ($extraCost->type === 'percentage') {
                $extraCostTotal += (float) $recipe->ingredients_cost_total * ((float) $extraCost->value / 100);
                continue;
            }

            $extraCostTotal += (float) $extraCost->value;
        }

        $recipe->extra_cost_total = $this->companyFormatter->roundMoney($extraCostTotal, $recipe->company);
    }

    protected function recalculatePackagingCosts(Recipe $recipe): void
    {
        $recipe->loadMissing('product.productPackagings');
        $packagingCostTotal = 0;

        foreach ($recipe->product?->productPackagings ?? [] as $productPackaging) {
            $packagingCostTotal += (float) $productPackaging->total_cost;
        }

        $recipe->packaging_cost_total = $this->companyFormatter->roundMoney($packagingCostTotal, $recipe->company);
    }

    protected function recalculateRecipeTotals(Recipe $recipe): void
    {
        $recipe->recipe_total_cost = $this->companyFormatter->roundMoney(
            $this->recipeCostService->calculateRecipeCost($recipe),
            $recipe->company,
        );
        $recipe->unit_cost = $this->companyFormatter->roundMoney(
            $this->recipeCostService->calculateUnitCost($recipe),
            $recipe->company,
        );
        $recipe->save();
    }

    protected function updateProductPricing(Recipe $recipe, Product $product, int $companyId): void
    {
        $product->loadMissing('company.setting');

        $suggestedSalePrice = $this->productPricingService->calculateSalePrice(
            $product,
            (float) $recipe->unit_cost
        );

        $recipe->suggested_sale_price = $suggestedSalePrice;
        $this->recipeRepository->save($recipe);

        $product->calculated_unit_cost = $this->companyFormatter->roundMoney((float) $recipe->unit_cost, $product->company);
        $product->suggested_sale_price = $suggestedSalePrice;
        $product = $this->productRepository->save($product);
        $this->productChannelPriceService->refreshForProduct($product, $companyId);
    }
}
