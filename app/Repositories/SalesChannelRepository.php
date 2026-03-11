<?php

namespace App\Repositories;

use App\Models\SalesChannel;
use Illuminate\Database\Eloquent\Collection;

class SalesChannelRepository
{
    public function getByCompany(int $companyId): Collection
    {
        return SalesChannel::with('fees')
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get();
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