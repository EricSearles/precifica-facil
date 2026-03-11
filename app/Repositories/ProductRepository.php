<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    public function getByCompany(int $companyId): Collection
    {
        return Product::with(['category', 'company.setting', 'productChannelPrices.salesChannel'])
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get();
    }

    public function findById(int $id, int $companyId): ?Product
    {
        return Product::with(['category', 'company.setting', 'productChannelPrices.salesChannel'])
            ->where('company_id', $companyId)
            ->where('id', $id)
            ->first();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function save(Product $product): Product
    {
        $product->save();

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}