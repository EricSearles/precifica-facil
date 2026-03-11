<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository
{
    public function getByCompany(int $companyId): Collection
    {
        return Product::with(['category', 'company.setting', 'productChannelPrices.salesChannel'])
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get();
    }

    public function getPaginatedByCompany(int $companyId, ?string $search = null, ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= (int) config('precificafacil.pagination.per_page', 10);

        return Product::with(['category', 'company.setting', 'productChannelPrices.salesChannel'])
            ->where('company_id', $companyId)
            ->when($search, fn ($query) => $query->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%")
                    ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"));
            }))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
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
