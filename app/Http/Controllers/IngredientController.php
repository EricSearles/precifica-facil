<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ingredients\StoreIngredientRequest;
use App\Http\Requests\Ingredients\UpdateIngredientRequest;
use App\Repositories\IngredientRepository;
use App\Services\IngredientService;
use InvalidArgumentException;
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
        $search = trim((string) $request->query('search', ''));
        $ingredients = $this->ingredientRepository->getPaginatedByCompany((int) $request->user()->company_id, $search);

        return view('ingredients.index', [
            'ingredients' => $ingredients,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('ingredients.create');
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

        try {
            $this->ingredientService->update($ingredientModel, $request->validated());
        } catch (InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }

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
