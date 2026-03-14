<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ingredients\StoreIngredientRequest;
use App\Http\Requests\Ingredients\UpdateIngredientRequest;
use App\Repositories\IngredientRepository;
use App\Services\IngredientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class IngredientController extends Controller
{
    public function __construct(
        protected IngredientRepository $ingredientRepository,
        protected IngredientService $ingredientService,
    ) {
    }

    public function index(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'status' => (string) $request->query('status', ''),
        ];
        $ingredients = $this->ingredientRepository->getPaginatedByCompany((int) $request->user()->company_id, $filters);

        return view('ingredients.index', [
            'ingredients' => $ingredients,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('ingredients.create');
    }

    public function search(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('q', ''));

        if (mb_strlen($search) < 2) {
            return response()->json([
                'results' => [],
            ]);
        }

        $ingredients = $this->ingredientRepository->searchByCompany((int) $request->user()->company_id, $search);

        return response()->json([
            'results' => $ingredients->map(fn ($ingredient) => [
                'id' => $ingredient->id,
                'name' => $ingredient->name,
                'brand' => $ingredient->brand,
                'base_unit' => $ingredient->base_unit,
            ])->values(),
        ]);
    }

    public function store(StoreIngredientRequest $request): RedirectResponse
    {
        try {
            $this->ingredientService->create($request->validated(), (int) $request->user()->company_id);
        } catch (InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Ingrediente adicionado. Ele já está pronto para entrar nas próximas receitas.');
    }

    public function edit(Request $request, int $ingredient): View
    {
        $ingredientModel = $this->ingredientRepository->findById($ingredient, (int) $request->user()->company_id);

        abort_if(! $ingredientModel, 404);

        return view('ingredients.edit', [
            'ingredient' => $ingredientModel,
        ]);
    }

    public function update(UpdateIngredientRequest $request, int $ingredient): RedirectResponse
    {
        $ingredientModel = $this->ingredientRepository->findById($ingredient, (int) $request->user()->company_id);

        abort_if(! $ingredientModel, 404);

        try {
            $this->ingredientService->update($ingredientModel, $request->validated());
        } catch (InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Ingrediente atualizado. Os próximos cálculos já vão usar esse custo revisado.');
    }

    public function destroy(Request $request, int $ingredient): RedirectResponse
    {
        $ingredientModel = $this->ingredientRepository->findById($ingredient, (int) $request->user()->company_id);

        abort_if(! $ingredientModel, 404);

        $this->ingredientService->delete($ingredientModel);

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Ingrediente removido da base da empresa.');
    }
}
