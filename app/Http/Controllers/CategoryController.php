<?php

namespace App\Http\Controllers;

use App\Http\Requests\Categories\StoreCategoryRequest;
use App\Http\Requests\Categories\UpdateCategoryRequest;
use App\Repositories\CategoryRepository;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryRepository $categoryRepository,
        protected CategoryService $categoryService,
    ) {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $categories = $this->categoryRepository->getPaginatedByCompany((int) $request->user()->company_id, $search);

        return view('categories.index', [
            'categories' => $categories,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->categoryService->create($request->validated(), (int) $request->user()->company_id);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoria criada com sucesso.');
    }

    public function edit(Request $request, int $category): View
    {
        $categoryModel = $this->categoryRepository->findById($category, (int) $request->user()->company_id);

        abort_if(! $categoryModel, 404);

        return view('categories.edit', [
            'category' => $categoryModel,
        ]);
    }

    public function update(UpdateCategoryRequest $request, int $category): RedirectResponse
    {
        $categoryModel = $this->categoryRepository->findById($category, (int) $request->user()->company_id);

        abort_if(! $categoryModel, 404);

        $this->categoryService->update($categoryModel, $request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(Request $request, int $category): RedirectResponse
    {
        $categoryModel = $this->categoryRepository->findById($category, (int) $request->user()->company_id);

        abort_if(! $categoryModel, 404);

        $this->categoryService->delete($categoryModel);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoria removida com sucesso.');
    }
}
