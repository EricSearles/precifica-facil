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

    public function getPaginatedByCompany(int $companyId, ?string $search = null, ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= (int) config('precificafacil.pagination.per_page', 10);

        return Ingredient::where('company_id', $companyId)
            ->when($search, fn ($query) => $query->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
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
