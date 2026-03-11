<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    public function getByCompany(int $companyId): Collection
    {
        return Category::where('company_id', $companyId)
            ->orderBy('name')
            ->get();
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
