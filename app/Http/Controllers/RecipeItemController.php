<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecipeItems\StoreRecipeItemRequest;
use App\Http\Requests\RecipeItems\UpdateRecipeItemRequest;
use App\Repositories\RecipeItemRepository;
use App\Repositories\RecipeRepository;
use App\Services\RecipeItemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class RecipeItemController extends Controller
{
    public function __construct(
        protected RecipeItemRepository $recipeItemRepository,
        protected RecipeRepository $recipeRepository,
        protected RecipeItemService $recipeItemService,
    ) {
    }

    public function store(StoreRecipeItemRequest $request): RedirectResponse
    {
        $recipe = $this->recipeRepository->findById((int) $request->validated()['recipe_id'], (int) $request->user()->company_id);

        abort_if(! $recipe, 404);

        try {
            $this->recipeItemService->create($request->validated(), (int) $request->user()->company_id);
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('recipes.show', $recipe->id)
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('recipes.show', $recipe->id)
            ->with('success', 'Ingrediente adicionado a receita com sucesso.');
    }

    public function update(UpdateRecipeItemRequest $request, int $recipeItem): RedirectResponse
    {
        $companyId = (int) $request->user()->company_id;
        $recipeItemModel = $this->recipeItemRepository->findById($recipeItem, $companyId);

        abort_if(! $recipeItemModel, 404);

        try {
            $this->recipeItemService->update($recipeItemModel, $request->validated(), $companyId);
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('recipes.show', $recipeItemModel->recipe_id)
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('recipes.show', $recipeItemModel->recipe_id)
            ->with('success', 'Item da receita atualizado com sucesso.');
    }

    public function destroy(Request $request, int $recipeItem): RedirectResponse
    {
        $companyId = (int) $request->user()->company_id;
        $recipeItemModel = $this->recipeItemRepository->findById($recipeItem, $companyId);

        abort_if(! $recipeItemModel, 404);

        $recipeId = (int) $recipeItemModel->recipe_id;
        $this->recipeItemService->delete($recipeItemModel, $companyId);

        return redirect()
            ->route('recipes.show', $recipeId)
            ->with('success', 'Item removido da receita com sucesso.');
    }
}
