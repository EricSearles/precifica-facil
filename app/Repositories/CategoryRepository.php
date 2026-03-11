<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryRepository
{
    public function getByCompany(int $companyId): Collection
    {
        return Category::where('company_id', $companyId)
            ->orderBy('name')
            ->get();
    }

    public function getPaginatedByCompany(int $companyId, ?string $search = null, ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= (int) config('precificafacil.pagination.per_page', 10);

        return Category::where('company_id', $companyId)
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id, int $companyId): ?Category
    {
        return Category::where('company_id', $companyId)
            ->where('id', $id)
            ->first();
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function save(Category $category): Category
    {
        $category->save();

        return $category;
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
