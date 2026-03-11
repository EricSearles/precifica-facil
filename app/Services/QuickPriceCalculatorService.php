<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Product;
use App\Models\SalesChannel;
use App\Support\CompanyFormatter;

class QuickPriceCalculatorService
{
    public function __construct(
        protected CompanyFormatter $companyFormatter,
        protected ChannelPricingService $channelPricingService,
    ) {
    }

    public function calculate(Company $company, array $data, ?SalesChannel $salesChannel = null): array
    {
        return $this->buildCalculation(
            company: $company,
            data: $data,
            packagingCost: (float) ($data['packaging_cost'] ?? 0),
            packagingCostPerUnit: null,
            salesChannel: $salesChannel,
            manualChannelRate: null,
            salesChannelName: $salesChannel?->name,
        );
    }

    public function calculatePublic(array $data): array
    {
        $yieldQuantity = (float) $data['yield_quantity'];
        $packagingCostPerUnit = (float) ($data['packaging_unit_cost'] ?? 0);

        return $this->buildCalculation(
            company: null,
            data: $data,
            packagingCost: $packagingCostPerUnit * $yieldQuantity,
            packagingCostPerUnit: $packagingCostPerUnit,
            salesChannel: null,
            manualChannelRate: (float) ($data['channel_percentage_rate'] ?? 0),
            salesChannelName: $data['sales_channel_name'] ?? null,
        );
    }

    protected function buildCalculation(
        ?Company $company,
        array $data,
        float $packagingCost,
        ?float $packagingCostPerUnit,
        ?SalesChannel $salesChannel,
        ?float $manualChannelRate,
        ?string $salesChannelName,
    ): array {
        $recipeCost = (float) $data['recipe_total_cost'];
        $yieldQuantity = (float) $data['yield_quantity'];
        $otherCosts = (float) ($data['other_costs'] ?? 0);
        $marginPercentage = (float) $data['profit_margin_percentage'];

        $totalCost = $this->companyFormatter->roundMoney(
            $recipeCost + $packagingCost + $otherCosts,
            $company,
        );

        $unitCost = $this->companyFormatter->roundMoney($totalCost / $yieldQuantity, $company);
        $baseSuggestedPrice = $this->companyFormatter->roundMoney(
            $unitCost * (1 + ($marginPercentage / 100)),
            $company,
        );

        $channelCalculation = null;
        $suggestedPrice = $baseSuggestedPrice;
        $profitPerUnit = $this->companyFormatter->roundMoney($baseSuggestedPrice - $unitCost, $company);

        if ($salesChannel) {
            $product = new Product([
                'suggested_sale_price' => $baseSuggestedPrice,
            ]);
            $product->setRelation('company', $company);

            $channelCalculation = $this->channelPricingService->calculate(
                $product,
                $salesChannel,
                $baseSuggestedPrice,
            );

            $suggestedPrice = (float) $channelCalculation['channel_price'];
            $profitPerUnit = $this->companyFormatter->roundMoney(
                (float) $channelCalculation['net_value'] - $unitCost,
                $company,
            );
        } elseif ($manualChannelRate !== null && $manualChannelRate > 0) {
            if ($manualChannelRate >= 100) {
                throw new InvalidArgumentException('A taxa percentual do canal deve ser menor que 100%.');
            }

            $suggestedPrice = $this->companyFormatter->roundMoney(
                $baseSuggestedPrice / (1 - ($manualChannelRate / 100)),
                $company,
            );

            $feeTotal = $this->companyFormatter->roundMoney($suggestedPrice - $baseSuggestedPrice, $company);

            $channelCalculation = [
                'reference_price' => $baseSuggestedPrice,
                'desired_net_value' => $baseSuggestedPrice,
                'percentage_fee_total' => $feeTotal,
                'fixed_fee_total' => 0.0,
                'fee_total' => $feeTotal,
                'channel_price' => $suggestedPrice,
                'net_value' => $baseSuggestedPrice,
                'percentage_rate' => round($manualChannelRate, 2),
            ];
        }

        return [
            'product_name' => $data['product_name'] ?? null,
            'recipe_total_cost' => $this->companyFormatter->roundMoney($recipeCost, $company),
            'packaging_cost' => $this->companyFormatter->roundMoney($packagingCost, $company),
            'packaging_unit_cost' => $this->companyFormatter->roundMoney((float) ($packagingCostPerUnit ?? 0), $company),
            'other_costs' => $this->companyFormatter->roundMoney($otherCosts, $company),
            'yield_quantity' => $yieldQuantity,
            'profit_margin_percentage' => $marginPercentage,
            'sales_channel_name' => $salesChannelName,
            'total_cost' => $totalCost,
            'unit_cost' => $unitCost,
            'minimum_price' => $unitCost,
            'base_suggested_price' => $baseSuggestedPrice,
            'suggested_price' => $suggestedPrice,
            'profit_per_unit' => $profitPerUnit,
            'profit_total' => $this->companyFormatter->roundMoney($profitPerUnit * $yieldQuantity, $company),
            'channel' => $channelCalculation,
        ];
    }
}
