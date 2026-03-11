<?php

namespace App\Repositories;

use App\Models\ExtraCost;
use Illuminate\Database\Eloquent\Collection;

class ExtraCostRepository
{
    public function getByRecipe(int $recipeId, int $companyId): Collection
    {
        return ExtraCost::where('company_id', $companyId)
            ->where('recipe_id', $recipeId)
            ->orderBy('id')
            ->get();
    }

    public function findById(int $id, int $companyId): ?ExtraCost
    {
        return ExtraCost::where('company_id', $companyId)
            ->where('id', $id)
            ->first();
    }

    public function create(array $data): ExtraCost
    {
        return ExtraCost::create($data);
    }

    public function save(ExtraCost $extraCost): ExtraCost
    {
        $extraCost->save();

        return $extraCost;
    }

    public function delete(ExtraCost $extraCost): void
    {
        $extraCost->delete();
    }
}
