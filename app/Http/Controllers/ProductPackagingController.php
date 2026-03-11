<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductPackagings\StoreProductPackagingRequest;
use App\Http\Requests\ProductPackagings\UpdateProductPackagingRequest;
use App\Repositories\ProductPackagingRepository;
use App\Services\ProductPackagingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductPackagingController extends Controller
{
    public function __construct(
        protected ProductPackagingRepository $productPackagingRepository,
        protected ProductPackagingService $productPackagingService,
    ) {
    }

    public function store(StoreProductPackagingRequest $request): RedirectResponse
    {
        $productPackaging = $this->productPackagingService->create($request->validated(), (int) $request->user()->company_id);

        return redirect()
            ->route('products.edit', $productPackaging->product_id)
            ->with('success', 'Embalagem vinculada ao produto com sucesso.');
    }

    public function update(UpdateProductPackagingRequest $request, int $productPackaging): RedirectResponse
    {
        $companyId = (int) $request->user()->company_id;
        $productPackagingModel = $this->productPackagingRepository->findById($productPackaging, $companyId);

        abort_if(! $productPackagingModel, 404);

        $this->productPackagingService->update($productPackagingModel, $request->validated(), $companyId);

        return redirect()
            ->route('products.edit', $productPackagingModel->product_id)
            ->with('success', 'Vinculo de embalagem atualizado com sucesso.');
    }

    public function destroy(Request $request, int $productPackaging): RedirectResponse
    {
        $companyId = (int) $request->user()->company_id;
        $productPackagingModel = $this->productPackagingRepository->findById($productPackaging, $companyId);

        abort_if(! $productPackagingModel, 404);

        $productId = (int) $productPackagingModel->product_id;
        $this->productPackagingService->delete($productPackagingModel, $companyId);

        return redirect()
            ->route('products.edit', $productId)
            ->with('success', 'Vinculo de embalagem removido com sucesso.');
    }
}
