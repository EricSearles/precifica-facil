<?php

namespace App\Services;

use App\Models\SalesChannelFee;
use App\Repositories\SalesChannelFeeRepository;

class SalesChannelFeeService
{
    public function __construct(
        protected SalesChannelFeeRepository $salesChannelFeeRepository,
        protected ProductChannelPriceService $productChannelPriceService,
    ) {
    }

    public function create(array $data, int $companyId): SalesChannelFee
    {
        $fee = $this->salesChannelFeeRepository->create([
            'company_id' => $companyId,
            'sales_channel_id' => $data['sales_channel_id'],
            'name' => $data['name'],
            'type' => $data['type'],
            'value' => $data['value'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        $this->productChannelPriceService->refreshForSalesChannel((int) $fee->sales_channel_id, $companyId);

        return $fee;
    }

    public function update(SalesChannelFee $salesChannelFee, array $data, int $companyId): SalesChannelFee
    {
        $salesChannelFee->name = $data['name'];
        $salesChannelFee->type = $data['type'];
        $salesChannelFee->value = $data['value'];
        $salesChannelFee->is_active = $data['is_active'] ?? false;

        $salesChannelFee = $this->salesChannelFeeRepository->save($salesChannelFee);
        $this->productChannelPriceService->refreshForSalesChannel((int) $salesChannelFee->sales_channel_id, $companyId);

        return $salesChannelFee;
    }

    public function delete(SalesChannelFee $salesChannelFee, int $companyId): void
    {
        $salesChannelId = (int) $salesChannelFee->sales_channel_id;

        $this->salesChannelFeeRepository->delete($salesChannelFee);
        $this->productChannelPriceService->refreshForSalesChannel($salesChannelId, $companyId);
    }
}