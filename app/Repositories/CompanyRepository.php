<?php

namespace App\Repositories;

use App\Models\Company;

class CompanyRepository
{
    public function create(array $data): Company
    {
        return Company::create($data);
    }

    public function existsBySlug(string $slug): bool
    {
        return Company::where('slug', $slug)->exists();
    }
}
