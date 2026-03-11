<?php

namespace App\Services;

use App\Models\Product;
use App\Support\CompanyFormatter;

class ProductPricingService
{
    public function __construct(
        protected CompanyFormatter $companyFormatter,
    ) {
    }

    public function calculateSalePrice(Product $product, float $unitCost): float
    {
        $product->loadMissing('company.setting');

        $marginType = $product->profit_margin_type;
        $marginValue = (float) $product->profit_margin_value;

        if ($product->use_global_margin) {
            $marginType = 'percentage';
            $marginValue = (float) optional($product->company?->setting)->default_profit_margin;
        }

        $salePrice = $marginType === 'percentage'
            ? $unitCost * (1 + ($marginValue / 100))
            : $unitCost + $marginValue;

        return $this->companyFormatter->roundMoney($salePrice, $product->company);
    }
}
