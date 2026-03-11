<?php

namespace App\Repositories;

use App\Models\SalesChannel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SalesChannelRepository
{
    public function getByCompany(int $companyId): Collection
    {
        return SalesChannel::with('fees')
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get();
    }

    public function getPaginatedByCompany(int $companyId, ?string $search = null, ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= (int) config('precificafacil.pagination.per_page', 10);

        return SalesChannel::with('fees')
            ->where('company_id', $companyId)
            ->when($search, fn ($query) => $query->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getActiveByCompany(int $companyId): Collection
    {
        return SalesChannel::with('fees')
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function findById(int $id, int $companyId): ?SalesChannel
    {
        return SalesChannel::with('fees')
            ->where('company_id', $companyId)
            ->where('id', $id)
            ->first();
    }

    public function create(array $data): SalesChannel
    {
        return SalesChannel::create($data);
    }

    public function save(SalesChannel $salesChannel): SalesChannel
    {
        $salesChannel->save();

        return $salesChannel;
    }

    public function delete(SalesChannel $salesChannel): void
    {
        $salesChannel->delete();
    }
}
