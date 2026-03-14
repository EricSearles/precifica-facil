<?php

namespace App\Repositories;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IngredientRepository
{
    public function getByCompany(int $companyId): Collection
    {
        return Ingredient::where('company_id', $companyId)
            ->orderBy('name')
            ->get();
    }

    public function getPaginatedByCompany(int $companyId, array $filters = [], ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= (int) config('precificafacil.pagination.per_page', 10);
        $search = trim((string) ($filters['search'] ?? ''));
        $status = (string) ($filters['status'] ?? '');

        return Ingredient::where('company_id', $companyId)
            ->when($search, fn ($query) => $query->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            }))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function searchByCompany(int $companyId, string $search, int $limit = 8): Collection
    {
        return Ingredient::where('company_id', $companyId)
            ->where('is_active', true)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            })
            ->orderByRaw('case when name like ? then 0 else 1 end', [$search.'%'])
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    public function findById(int $id, int $companyId): ?Ingredient
    {
        return Ingredient::where('company_id', $companyId)
            ->where('id', $id)
            ->first();
    }

    public function create(array $data): Ingredient
    {
        return Ingredient::create($data);
    }

    public function save(Ingredient $ingredient): Ingredient
    {
        $ingredient->save();

        return $ingredient;
    }

    public function delete(Ingredient $ingredient): void
    {
        $ingredient->delete();
    }
}
