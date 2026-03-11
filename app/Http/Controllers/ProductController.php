<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\PackagingRepository;
use App\Repositories\ProductChannelPriceRepository;
use App\Repositories\ProductPackagingRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SalesChannelRepository;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected ProductRepository $productRepository,
        protected CategoryRepository $categoryRepository,
        protected PackagingRepository $packagingRepository,
        protected ProductPackagingRepository $productPackagingRepository,
        protected SalesChannelRepository $salesChannelRepository,
        protected ProductChannelPriceRepository $productChannelPriceRepository,
        protected ProductService $productService,
    ) {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $products = $this->productRepository->getPaginatedByCompany((int) $request->user()->company_id, $search);

        return view('products.index', [
            'products' => $products,
            'search' => $search,
        ]);
    }

    public function create(Request $request): View
    {
        $categories = $this->categoryRepository->getByCompany((int) $request->user()->company_id);

        return view('products.create', [
            'categories' => $categories,
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productService->create($request->validated(), (int) $request->user()->company_id);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produto criado com sucesso.');
    }

    public function edit(Request $request, int $product): View
    {
        $companyId = (int) $request->user()->company_id;
        $productModel = $this->productRepository->findById($product, $companyId);

        abort_if(! $productModel, 404);

        return view('products.edit', [
            'product' => $productModel,
            'categories' => $this->categoryRepository->getByCompany($companyId),
            'packagings' => $this->packagingRepository->getByCompany($companyId),
            'productPackagings' => $this->productPackagingRepository->getByProduct($productModel->id, $companyId),
            'salesChannels' => $this->salesChannelRepository->getActiveByCompany($companyId),
            'productChannelPrices' => $this->productChannelPriceRepository->getByProduct($productModel->id, $companyId),
        ]);
    }

    public function update(UpdateProductRequest $request, int $product): RedirectResponse
    {
        $productModel = $this->productRepository->findById($product, (int) $request->user()->company_id);

        abort_if(! $productModel, 404);

        $this->productService->update($productModel, $request->validated());

        return redirect()
            ->route('products.index')
            ->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy(Request $request, int $product): RedirectResponse
    {
        $productModel = $this->productRepository->findById($product, (int) $request->user()->company_id);

        abort_if(! $productModel, 404);

        $this->productService->delete($productModel);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produto removido com sucesso.');
    }
}
