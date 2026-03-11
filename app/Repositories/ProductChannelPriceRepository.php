<?php

namespace App\Repositories;

use App\Models\ProductChannelPrice;
use Illuminate\Database\Eloquent\Collection;

class ProductChannelPriceRepository
{
    public function getByProduct(int $productId, int $companyId): Collection
    {
        return ProductChannelPrice::with('salesChannel.fees')
            ->where('company_id', $companyId)
            ->where('product_id', $productId)
            ->orderBy('sales_channel_id')
            ->get();
    }

    public function getBySalesChannel(int $salesChannelId, int $companyId): Collection
    {
        return ProductChannelPrice::with('product.company.setting', 'salesChannel.fees')
            ->where('company_id', $companyId)
            ->where('sales_channel_id', $salesChannelId)
            ->orderBy('product_id')
            ->get();
    }

    public function findById(int $id, int $companyId): ?ProductChannelPrice
    {
        return ProductChannelPrice::with('salesChannel.fees', 'product.company.setting')
            ->where('company_id', $companyId)
            ->where('id', $id)
            ->first();
    }

    public function findByProductAndChannel(int $productId, int $salesChannelId, int $companyId): ?ProductChannelPrice
    {
        return ProductChannelPrice::with('salesChannel.fees', 'product.company.setting')
            ->where('company_id', $companyId)
            ->where('product_id', $productId)
            ->where('sales_channel_id', $salesChannelId)
            ->first();
    }

    public function create(array $data): ProductChannelPrice
    {
        return ProductChannelPrice::create($data);
    }

    public function save(ProductChannelPrice $productChannelPrice): ProductChannelPrice
    {
        $productChannelPrice->save();

        return $productChannelPrice;
    }

    public function delete(ProductChannelPrice $productChannelPrice): void
    {
        $productChannelPrice->delete();
    }
}