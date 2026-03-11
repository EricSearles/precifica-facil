<?php

namespace App\Repositories;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Collection;

class IngredientRepository
{
    public function getByCompany(int $companyId): Collection
    {
        return Ingredient::where('company_id', $companyId)
            ->orderBy('name')
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
