<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ingredients\StoreIngredientRequest;
use App\Http\Requests\Ingredients\UpdateIngredientRequest;
use App\Repositories\IngredientRepository;
use App\Services\IngredientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IngredientController extends Controller
{
    public function __construct(
        protected IngredientRepository $ingredientRepository,
        protected IngredientService $ingredientService,
    ) {
    }

    public function index(Request $request): View
    {
        $ingredients = $this->ingredientRepository->getByCompany((int) $request->user()->company_id);

        return view('ingredients.index', [
            'ingredients' => $ingredients,
        ]);
    }

    public function create(): View
    {
        return view('ingredients.create');
    }

    public function store(StoreIngredientRequest $request): RedirectResponse
    {
        $this->ingredientService->create($request->validated(), (int) $request->user()->company_id);

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Ingrediente criado com sucesso.');
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

        $this->ingredientService->update($ingredientModel, $request->validated());

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Ingrediente atualizado com sucesso.');
    }

    public function destroy(Request $request, int $ingredient): RedirectResponse
    {
        $ingredientModel = $this->ingredientRepository->findById($ingredient, (int) $request->user()->company_id);

        abort_if(! $ingredientModel, 404);

        $this->ingredientService->delete($ingredientModel);

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Ingrediente removido com sucesso.');
    }
}
