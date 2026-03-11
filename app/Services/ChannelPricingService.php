<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SalesChannel;
use App\Support\CompanyFormatter;
use InvalidArgumentException;

class ChannelPricingService
{
    public function __construct(
        protected CompanyFormatter $companyFormatter,
    ) {
    }

    public function calculate(Product $product, SalesChannel $salesChannel, ?float $desiredNetValue = null): array
    {
        $product->loadMissing('company.setting');
        $salesChannel->loadMissing('fees');

        $referencePrice = $this->companyFormatter->roundMoney((float) $product->suggested_sale_price, $product->company);
        $desiredNetValue = $this->companyFormatter->roundMoney(
            $desiredNetValue ?? (float) $product->suggested_sale_price,
            $product->company,
        );

        $percentageRate = (float) $salesChannel->fees
            ->where('is_active', true)
            ->where('type', 'percentage')
            ->sum(fn ($fee) => (float) $fee->value);

        $fixedFeeTotal = (float) $salesChannel->fees
            ->where('is_active', true)
            ->where('type', 'fixed')
            ->sum(fn ($fee) => (float) $fee->value);

        if ($percentageRate >= 100) {
            throw new InvalidArgumentException('A soma das taxas percentuais do canal deve ser menor que 100%.');
        }

        $channelPrice = ($desiredNetValue + $fixedFeeTotal) / (1 - ($percentageRate / 100));
        $percentageFeeTotal = $channelPrice * ($percentageRate / 100);
        $feeTotal = $percentageFeeTotal + $fixedFeeTotal;
        $netValue = $channelPrice - $feeTotal;

        return [
            'reference_price' => $referencePrice,
            'desired_net_value' => $desiredNetValue,
            'percentage_fee_total' => $this->companyFormatter->roundMoney($percentageFeeTotal, $product->company),
            'fixed_fee_total' => $this->companyFormatter->roundMoney($fixedFeeTotal, $product->company),
            'fee_total' => $this->companyFormatter->roundMoney($feeTotal, $product->company),
            'channel_price' => $this->companyFormatter->roundMoney($channelPrice, $product->company),
            'net_value' => $this->companyFormatter->roundMoney($netValue, $product->company),
            'percentage_rate' => round($percentageRate, 2),
        ];
    }
}