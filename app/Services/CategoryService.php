<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;

class CategoryService
{
    public function __construct(
        protected CategoryRepository $categoryRepository,
    ) {
    }

    public function create(array $data, int $companyId): Category
    {
        return $this->categoryRepository->create([
            'company_id' => $companyId,
            'name' => $data['name'],
        ]);
    }

    public function update(Category $category, array $data): Category
    {
        $category->name = $data['name'];

        return $this->categoryRepository->save($category);
    }

    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }
}
