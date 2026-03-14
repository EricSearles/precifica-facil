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

    public function getPaginatedByCompany(int $companyId, array $filters = [], ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= (int) config('precificafacil.pagination.per_page', 10);
        $search = trim((string) ($filters['search'] ?? ''));
        $status = (string) ($filters['status'] ?? '');
        $categoryId = (int) ($filters['category_id'] ?? 0);

        return Product::with(['category', 'company.setting', 'productChannelPrices.salesChannel'])
            ->where('company_id', $companyId)
            ->when($search, fn ($query) => $query->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%")
                    ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"));
            }))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when($categoryId > 0, fn ($query) => $query->where('category_id', $categoryId))
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
