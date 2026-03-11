<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductChannelPrices\StoreProductChannelPriceRequest;
use App\Http\Requests\ProductChannelPrices\UpdateProductChannelPriceRequest;
use App\Repositories\ProductChannelPriceRepository;
use App\Services\ProductChannelPriceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ProductChannelPriceController extends Controller
{
    public function __construct(
        protected ProductChannelPriceRepository $productChannelPriceRepository,
        protected ProductChannelPriceService $productChannelPriceService,
    ) {
    }

    public function store(StoreProductChannelPriceRequest $request): RedirectResponse
    {
        try {
            $productChannelPrice = $this->productChannelPriceService->createOrUpdate(
                $request->validated(),
                (int) $request->user()->company_id,
            );
        } catch (InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->withErrors(['desired_net_value' => $exception->getMessage()]);
        }

        return redirect()
            ->route('products.edit', $productChannelPrice->product_id)
            ->with('success', 'Preço por canal salvo com sucesso.');
    }

    public function update(UpdateProductChannelPriceRequest $request, int $productChannelPrice): RedirectResponse
    {
        $companyId = (int) $request->user()->company_id;
        $productChannelPriceModel = $this->productChannelPriceRepository->findById($productChannelPrice, $companyId);

        abort_if(! $productChannelPriceModel, 404);

        try {
            $this->productChannelPriceService->update($productChannelPriceModel, $request->validated());
        } catch (InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->withErrors(['desired_net_value' => $exception->getMessage()]);
        }

        return redirect()
            ->route('products.edit', $productChannelPriceModel->product_id)
            ->with('success', 'Preço do canal atualizado com sucesso.');
    }

    public function destroy(Request $request, int $productChannelPrice): RedirectResponse
    {
        $productChannelPriceModel = $this->productChannelPriceRepository->findById($productChannelPrice, (int) $request->user()->company_id);

        abort_if(! $productChannelPriceModel, 404);

        $productId = (int) $productChannelPriceModel->product_id;
        $this->productChannelPriceService->delete($productChannelPriceModel);

        return redirect()
            ->route('products.edit', $productId)
            ->with('success', 'Preço do canal removido com sucesso.');
    }
}