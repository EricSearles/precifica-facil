<?php

namespace App\Repositories;

use App\Models\SalesChannelFee;
use Illuminate\Database\Eloquent\Collection;

class SalesChannelFeeRepository
{
    public function getByChannel(int $salesChannelId, int $companyId): Collection
    {
        return SalesChannelFee::where('company_id', $companyId)
            ->where('sales_channel_id', $salesChannelId)
            ->orderBy('name')
            ->get();
    }

    public function getActiveByChannel(int $salesChannelId, int $companyId): Collection
    {
        return SalesChannelFee::where('company_id', $companyId)
            ->where('sales_channel_id', $salesChannelId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function findById(int $id, int $companyId): ?SalesChannelFee
    {
        return SalesChannelFee::where('company_id', $companyId)
            ->where('id', $id)
            ->first();
    }

    public function create(array $data): SalesChannelFee
    {
        return SalesChannelFee::create($data);
    }

    public function save(SalesChannelFee $fee): SalesChannelFee
    {
        $fee->save();

        return $fee;
    }

    public function delete(SalesChannelFee $fee): void
    {
        $fee->delete();
    }
}
