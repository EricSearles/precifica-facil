<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesChannels\StoreSalesChannelRequest;
use App\Http\Requests\SalesChannels\UpdateSalesChannelRequest;
use App\Repositories\SalesChannelRepository;
use App\Services\SalesChannelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesChannelController extends Controller
{
    public function __construct(
        protected SalesChannelRepository $salesChannelRepository,
        protected SalesChannelService $salesChannelService,
    ) {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $salesChannels = $this->salesChannelRepository->getPaginatedByCompany((int) $request->user()->company_id, $search);

        return view('sales-channels.index', [
            'salesChannels' => $salesChannels,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('sales-channels.create');
    }

    public function store(StoreSalesChannelRequest $request): RedirectResponse
    {
        $salesChannel = $this->salesChannelService->create($request->validated(), (int) $request->user()->company_id);

        return redirect()
            ->route('sales-channels.edit', $salesChannel->id)
            ->with('success', 'Canal de venda criado com sucesso.');
    }

    public function edit(Request $request, int $salesChannel): View
    {
        $salesChannelModel = $this->salesChannelRepository->findById($salesChannel, (int) $request->user()->company_id);

        abort_if(! $salesChannelModel, 404);

        return view('sales-channels.edit', [
            'salesChannel' => $salesChannelModel,
        ]);
    }

    public function update(UpdateSalesChannelRequest $request, int $salesChannel): RedirectResponse
    {
        $companyId = (int) $request->user()->company_id;
        $salesChannelModel = $this->salesChannelRepository->findById($salesChannel, $companyId);

        abort_if(! $salesChannelModel, 404);

        $this->salesChannelService->update($salesChannelModel, $request->validated(), $companyId);

        return redirect()
            ->route('sales-channels.edit', $salesChannelModel->id)
            ->with('success', 'Canal de venda atualizado com sucesso.');
    }

    public function destroy(Request $request, int $salesChannel): RedirectResponse
    {
        $salesChannelModel = $this->salesChannelRepository->findById($salesChannel, (int) $request->user()->company_id);

        abort_if(! $salesChannelModel, 404);

        $this->salesChannelService->delete($salesChannelModel);

        return redirect()
            ->route('sales-channels.index')
            ->with('success', 'Canal de venda removido com sucesso.');
    }
}
