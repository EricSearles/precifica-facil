<?php

namespace App\Http\Controllers;

use App\Http\Requests\Recipes\StoreRecipeRequest;
use App\Http\Requests\Recipes\UpdateRecipeRequest;
use App\Repositories\ProductRepository;
use App\Repositories\RecipeRepository;
use App\Services\RecipeCrudService;
use App\Services\RecipeService;
use App\Services\UnitConversionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class RecipeController extends Controller
{
    public function __construct(
        protected RecipeRepository $recipeRepository,
        protected ProductRepository $productRepository,
        protected RecipeCrudService $recipeCrudService,
        protected RecipeService $recipeService,
    ) {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $recipes = $this->recipeRepository->getPaginatedByCompany((int) $request->user()->company_id, $search);

        return view('recipes.index', [
            'recipes' => $recipes,
            'search' => $search,
        ]);
    }

    public function create(Request $request): View
    {
        $products = $this->productRepository->getByCompany((int) $request->user()->company_id);

        return view('recipes.create', [
            'products' => $products,
        ]);
    }

    public function store(StoreRecipeRequest $request): RedirectResponse
    {
        $recipe = $this->recipeCrudService->create($request->validated(), (int) $request->user()->company_id);

        return redirect()
            ->route('recipes.show', $recipe->id)
            ->with('success', 'Receita criada com sucesso. Agora adicione os ingredientes da composicao.');
    }

    public function show(Request $request, int $recipe): View
    {
        $companyId = (int) $request->user()->company_id;
        $recipeModel = $this->recipeRepository->findWithItems($recipe, $companyId);
        $ingredients = app(\App\Repositories\IngredientRepository::class)->getByCompany($companyId);

        abort_if(! $recipeModel, 404);

        return view('recipes.show', [
            'recipe' => $recipeModel,
            'ingredients' => $ingredients,
            'ingredientUnitOptions' => app(UnitConversionService::class)
                ->compatibleUnitsForIngredientMap($ingredients),
        ]);
    }

    public function edit(Request $request, int $recipe): View
    {
        $companyId = (int) $request->user()->company_id;
        $recipeModel = $this->recipeRepository->findWithItems($recipe, $companyId);

        abort_if(! $recipeModel, 404);

        return view('recipes.edit', [
            'recipe' => $recipeModel,
            'products' => $this->productRepository->getByCompany($companyId),
        ]);
    }

    public function update(UpdateRecipeRequest $request, int $recipe): RedirectResponse
    {
        $recipeModel = $this->recipeRepository->findById($recipe, (int) $request->user()->company_id);

        abort_if(! $recipeModel, 404);

        $this->recipeCrudService->update($recipeModel, $request->validated());

        return redirect()
            ->route('recipes.show', $recipeModel->id)
            ->with('success', 'Receita atualizada com sucesso.');
    }

    public function destroy(Request $request, int $recipe): RedirectResponse
    {
        $recipeModel = $this->recipeRepository->findById($recipe, (int) $request->user()->company_id);

        abort_if(! $recipeModel, 404);

        $this->recipeCrudService->delete($recipeModel);

        return redirect()
            ->route('recipes.index')
            ->with('success', 'Receita removida com sucesso.');
    }

    public function recalculate(Request $request, int $recipe): RedirectResponse|JsonResponse
    {
        $companyId = (int) $request->user()->company_id;
        try {
            $updatedRecipe = $this->recipeService->recalculateAndUpdate($recipe, $companyId);
        } catch (InvalidArgumentException $exception) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $exception->getMessage(),
                ], 422);
            }

            return redirect()
                ->route('recipes.show', $recipe)
                ->with('error', $exception->getMessage());
        }

        if (! $updatedRecipe) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Receita nao encontrada para a empresa do usuario autenticado.',
                ], 404);
            }

            return redirect()
                ->route('recipes.index')
                ->with('error', 'Receita nao encontrada para a empresa do usuario autenticado.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Receita recalculada com sucesso.',
                'data' => $updatedRecipe,
            ]);
        }

        return redirect()
            ->route('recipes.show', $updatedRecipe->id)
            ->with('success', 'Receita recalculada com sucesso.');
    }
}
