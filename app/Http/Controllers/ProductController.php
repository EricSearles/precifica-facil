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
        $companyId = (int) $request->user()->company_id;
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'status' => (string) $request->query('status', ''),
            'category_id' => (int) $request->query('category_id', 0),
        ];
        $products = $this->productRepository->getPaginatedByCompany($companyId, $filters);

        return view('products.index', [
            'products' => $products,
            'filters' => $filters,
            'categories' => $this->categoryRepository->getByCompany($companyId),
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
            ->with('success', 'Produto criado. Agora você já pode revisar margem, canais e embalagem.');
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
            ->with('success', 'Produto atualizado. O preço sugerido e os canais foram revisados com a nova configuração.');
    }

    public function destroy(Request $request, int $product): RedirectResponse
    {
        $productModel = $this->productRepository->findById($product, (int) $request->user()->company_id);

        abort_if(! $productModel, 404);

        $this->productService->delete($productModel);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produto removido do catálogo da empresa.');
    }

    public function duplicate(Request $request, int $product): RedirectResponse
    {
        $productModel = $this->productRepository->findById($product, (int) $request->user()->company_id);

        abort_if(! $productModel, 404);

        $duplicate = $this->productService->duplicate($productModel);

        return redirect()
            ->route('products.edit', $duplicate->id)
            ->with('success', 'Produto duplicado. Revise margem, embalagem e canais antes de publicar.');
    }
}
