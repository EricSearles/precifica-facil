<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductChannelPrice;
use App\Repositories\ProductChannelPriceRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SalesChannelRepository;
use InvalidArgumentException;

class ProductChannelPriceService
{
    public function __construct(
        protected ProductChannelPriceRepository $productChannelPriceRepository,
        protected ProductRepository $productRepository,
        protected SalesChannelRepository $salesChannelRepository,
        protected ChannelPricingService $channelPricingService,
    ) {
    }

    public function createOrUpdate(array $data, int $companyId): ProductChannelPrice
    {
        $product = $this->productRepository->findById((int) $data['product_id'], $companyId);
        $salesChannel = $this->salesChannelRepository->findById((int) $data['sales_channel_id'], $companyId);

        if (! $product || ! $salesChannel) {
            throw new InvalidArgumentException('Produto ou canal de venda não encontrado.');
        }

        $calculated = $this->channelPricingService->calculate(
            $product,
            $salesChannel,
            array_key_exists('desired_net_value', $data) ? (float) $data['desired_net_value'] : null,
        );

        $productChannelPrice = $this->productChannelPriceRepository->findByProductAndChannel(
            (int) $product->id,
            (int) $salesChannel->id,
            $companyId,
        );

        if (! $productChannelPrice) {
            return $this->productChannelPriceRepository->create([
                'company_id' => $companyId,
                'product_id' => $product->id,
                'sales_channel_id' => $salesChannel->id,
                'reference_price' => $calculated['reference_price'],
                'desired_net_value' => $calculated['desired_net_value'],
                'percentage_fee_total' => $calculated['percentage_fee_total'],
                'fixed_fee_total' => $calculated['fixed_fee_total'],
                'fee_total' => $calculated['fee_total'],
                'channel_price' => $calculated['channel_price'],
                'net_value' => $calculated['net_value'],
            ]);
        }

        return $this->fillAndSave($productChannelPrice, $calculated);
    }

    public function update(ProductChannelPrice $productChannelPrice, array $data): ProductChannelPrice
    {
        $productChannelPrice->loadMissing('product.company.setting', 'salesChannel.fees');

        $calculated = $this->channelPricingService->calculate(
            $productChannelPrice->product,
            $productChannelPrice->salesChannel,
            array_key_exists('desired_net_value', $data) ? (float) $data['desired_net_value'] : null,
        );

        return $this->fillAndSave($productChannelPrice, $calculated);
    }

    public function delete(ProductChannelPrice $productChannelPrice): void
    {
        $this->productChannelPriceRepository->delete($productChannelPrice);
    }

    public function refreshForProduct(Product $product, int $companyId): void
    {
        $product->loadMissing('company.setting');
        $prices = $this->productChannelPriceRepository->getByProduct((int) $product->id, $companyId);

        foreach ($prices as $price) {
            $price->loadMissing('salesChannel.fees');

            $desiredNetValue = $this->shouldFollowReferencePrice($price)
                ? (float) $product->suggested_sale_price
                : (float) $price->desired_net_value;

            $calculated = $this->channelPricingService->calculate($product, $price->salesChannel, $desiredNetValue);
            $this->fillAndSave($price, $calculated);
        }
    }

    public function refreshForSalesChannel(int $salesChannelId, int $companyId): void
    {
        $prices = $this->productChannelPriceRepository->getBySalesChannel($salesChannelId, $companyId);

        foreach ($prices as $price) {
            $price->loadMissing('product.company.setting', 'salesChannel.fees');

            $desiredNetValue = $this->shouldFollowReferencePrice($price)
                ? (float) $price->product->suggested_sale_price
                : (float) $price->desired_net_value;

            $calculated = $this->channelPricingService->calculate($price->product, $price->salesChannel, $desiredNetValue);
            $this->fillAndSave($price, $calculated);
        }
    }

    protected function fillAndSave(ProductChannelPrice $productChannelPrice, array $calculated): ProductChannelPrice
    {
        $productChannelPrice->reference_price = $calculated['reference_price'];
        $productChannelPrice->desired_net_value = $calculated['desired_net_value'];
        $productChannelPrice->percentage_fee_total = $calculated['percentage_fee_total'];
        $productChannelPrice->fixed_fee_total = $calculated['fixed_fee_total'];
        $productChannelPrice->fee_total = $calculated['fee_total'];
        $productChannelPrice->channel_price = $calculated['channel_price'];
        $productChannelPrice->net_value = $calculated['net_value'];

        return $this->productChannelPriceRepository->save($productChannelPrice);
    }

    protected function shouldFollowReferencePrice(ProductChannelPrice $productChannelPrice): bool
    {
        return abs((float) $productChannelPrice->desired_net_value - (float) $productChannelPrice->reference_price) < 0.01;
    }
}