<?php

namespace App\Repositories;

use App\Models\ProductPackaging;
use Illuminate\Database\Eloquent\Collection;

class ProductPackagingRepository
{
    public function getByProduct(int $productId, int $companyId): Collection
    {
        return ProductPackaging::with('packaging')
            ->where('company_id', $companyId)
            ->where('product_id', $productId)
            ->orderBy('id')
            ->get();
    }

    public function findById(int $id, int $companyId): ?ProductPackaging
    {
        return ProductPackaging::with(['product', 'packaging'])
            ->where('company_id', $companyId)
            ->where('id', $id)
            ->first();
    }

    public function create(array $data): ProductPackaging
    {
        return ProductPackaging::create($data);
    }

    public function save(ProductPackaging $productPackaging): ProductPackaging
    {
        $productPackaging->save();

        return $productPackaging;
    }

    public function delete(ProductPackaging $productPackaging): void
    {
        $productPackaging->delete();
    }
}
