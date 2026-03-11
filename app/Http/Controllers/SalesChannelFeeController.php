<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesChannelFees\StoreSalesChannelFeeRequest;
use App\Http\Requests\SalesChannelFees\UpdateSalesChannelFeeRequest;
use App\Repositories\SalesChannelFeeRepository;
use App\Services\SalesChannelFeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SalesChannelFeeController extends Controller
{
    public function __construct(
        protected SalesChannelFeeRepository $salesChannelFeeRepository,
        protected SalesChannelFeeService $salesChannelFeeService,
    ) {
    }

    public function store(StoreSalesChannelFeeRequest $request): RedirectResponse
    {
        $fee = $this->salesChannelFeeService->create($request->validated(), (int) $request->user()->company_id);

        return redirect()
            ->route('sales-channels.edit', $fee->sales_channel_id)
            ->with('success', 'Taxa adicionada com sucesso.');
    }

    public function update(UpdateSalesChannelFeeRequest $request, int $salesChannelFee): RedirectResponse
    {
        $companyId = (int) $request->user()->company_id;
        $salesChannelFeeModel = $this->salesChannelFeeRepository->findById($salesChannelFee, $companyId);

        abort_if(! $salesChannelFeeModel, 404);

        $this->salesChannelFeeService->update($salesChannelFeeModel, $request->validated(), $companyId);

        return redirect()
            ->route('sales-channels.edit', $salesChannelFeeModel->sales_channel_id)
            ->with('success', 'Taxa atualizada com sucesso.');
    }

    public function destroy(Request $request, int $salesChannelFee): RedirectResponse
    {
        $companyId = (int) $request->user()->company_id;
        $salesChannelFeeModel = $this->salesChannelFeeRepository->findById($salesChannelFee, $companyId);

        abort_if(! $salesChannelFeeModel, 404);

        $salesChannelId = (int) $salesChannelFeeModel->sales_channel_id;
        $this->salesChannelFeeService->delete($salesChannelFeeModel, $companyId);

        return redirect()
            ->route('sales-channels.edit', $salesChannelId)
            ->with('success', 'Taxa removida com sucesso.');
    }
}