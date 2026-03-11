<?php

namespace App\Repositories;

use App\Models\Packaging;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PackagingRepository
{
    public function getByCompany(int $companyId): Collection
    {
        return Packaging::where('company_id', $companyId)
            ->orderBy('name')
            ->get();
    }

    public function getPaginatedByCompany(int $companyId, ?string $search = null, ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= (int) config('precificafacil.pagination.per_page', 10);

        return Packaging::where('company_id', $companyId)
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id, int $companyId): ?Packaging
    {
        return Packaging::where('company_id', $companyId)
            ->where('id', $id)
            ->first();
    }

    public function create(array $data): Packaging
    {
        return Packaging::create($data);
    }

    public function save(Packaging $packaging): Packaging
    {
        $packaging->save();

        return $packaging;
    }

    public function delete(Packaging $packaging): void
    {
        $packaging->delete();
    }
}
