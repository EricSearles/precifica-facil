<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductChannelPrice;
use App\Models\ProductPackaging;
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

    public function duplicate(Product $product): Product
    {
        $duplicate = $this->productRepository->create([
            'company_id' => $product->company_id,
            'category_id' => $product->category_id,
            'name' => $this->duplicateName($product->name),
            'sale_unit' => $product->sale_unit,
            'yield_quantity' => $product->yield_quantity,
            'profit_margin_type' => $product->profit_margin_type,
            'profit_margin_value' => $product->profit_margin_value,
            'use_global_margin' => $product->use_global_margin,
            'calculated_unit_cost' => (float) $product->calculated_unit_cost,
            'suggested_sale_price' => (float) $product->suggested_sale_price,
            'notes' => $product->notes,
            'is_active' => $product->is_active,
        ]);

        $product->loadMissing('productPackagings', 'productChannelPrices');

        foreach ($product->productPackagings as $packaging) {
            ProductPackaging::query()->create([
                'company_id' => $duplicate->company_id,
                'product_id' => $duplicate->id,
                'packaging_id' => $packaging->packaging_id,
                'quantity' => $packaging->quantity,
                'total_cost' => $packaging->total_cost,
            ]);
        }

        foreach ($product->productChannelPrices as $channelPrice) {
            ProductChannelPrice::query()->create([
                'company_id' => $duplicate->company_id,
                'product_id' => $duplicate->id,
                'sales_channel_id' => $channelPrice->sales_channel_id,
                'reference_price' => $channelPrice->reference_price,
                'desired_net_value' => $channelPrice->desired_net_value,
                'percentage_fee_total' => $channelPrice->percentage_fee_total,
                'fixed_fee_total' => $channelPrice->fixed_fee_total,
                'fee_total' => $channelPrice->fee_total,
                'channel_price' => $channelPrice->channel_price,
                'net_value' => $channelPrice->net_value,
            ]);
        }

        return $duplicate->fresh(['category', 'company.setting', 'productChannelPrices.salesChannel']);
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

    private function duplicateName(string $name): string
    {
        return str($name)->finish('')->append(' (Cópia)')->toString();
    }
}
