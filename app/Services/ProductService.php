<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;

class ProductService
{
    public function __construct(
        protected ProductRepository $productRepository,
        protected ProductPricingService $productPricingService,
        protected ProductChannelPriceService $productChannelPriceService,
    ) {
    }

    public function create(array $data, int $companyId): Product
    {
        $product = $this->productRepository->create([
            'company_id' => $companyId,
            'category_id' => $data['category_id'] ?: null,
            'name' => $data['name'],
            'sale_unit' => $data['sale_unit'],
            'yield_quantity' => $data['yield_quantity'],
            'profit_margin_type' => $data['profit_margin_type'],
            'profit_margin_value' => $data['profit_margin_value'],
            'use_global_margin' => $data['use_global_margin'] ?? false,
            'calculated_unit_cost' => 0,
            'suggested_sale_price' => 0,
            'notes' => $data['notes'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        $product->suggested_sale_price = $this->calculateSuggestedSalePrice($product);
        $product = $this->productRepository->save($product);
        $this->productChannelPriceService->refreshForProduct($product, (int) $product->company_id);

        return $product;
    }

    public function update(Product $product, array $data): Product
    {
        $product->category_id = $data['category_id'] ?: null;
        $product->name = $data['name'];
        $product->sale_unit = $data['sale_unit'];
        $product->yield_quantity = $data['yield_quantity'];
        $product->profit_margin_type = $data['profit_margin_type'];
        $product->profit_margin_value = $data['profit_margin_value'];
        $product->use_global_margin = $data['use_global_margin'] ?? false;
        $product->notes = $data['notes'] ?? null;
        $product->is_active = $data['is_active'] ?? false;
        $product->suggested_sale_price = $this->calculateSuggestedSalePrice($product);

        $product = $this->productRepository->save($product);
        $this->productChannelPriceService->refreshForProduct($product, (int) $product->company_id);

        return $product;
    }

    public function delete(Product $product): void
    {
        $this->productRepository->delete($product);
    }

    protected function calculateSuggestedSalePrice(Product $product): float
    {
        $pricingProduct = clone $product;

        if ($product->use_global_margin) {
            $product->loadMissing('company.setting');
            $pricingProduct->profit_margin_type = 'percentage';
            $pricingProduct->profit_margin_value = (float) optional($product->company?->setting)->default_profit_margin;
        }

        return $this->productPricingService->calculateSalePrice(
            $pricingProduct,
            (float) $product->calculated_unit_cost
        );
    }
}