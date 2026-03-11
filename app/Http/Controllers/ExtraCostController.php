<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExtraCosts\StoreExtraCostRequest;
use App\Http\Requests\ExtraCosts\UpdateExtraCostRequest;
use App\Repositories\ExtraCostRepository;
use App\Services\ExtraCostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExtraCostController extends Controller
{
    public function __construct(
        protected ExtraCostRepository $extraCostRepository,
        protected ExtraCostService $extraCostService,
    ) {
    }

    public function store(StoreExtraCostRequest $request): RedirectResponse
    {
        $extraCost = $this->extraCostService->create($request->validated(), (int) $request->user()->company_id);

        return redirect()
            ->route('recipes.show', $extraCost->recipe_id)
            ->with('success', 'Custo extra adicionado com sucesso.');
    }

    public function update(UpdateExtraCostRequest $request, int $extraCost): RedirectResponse
    {
        $companyId = (int) $request->user()->company_id;
        $extraCostModel = $this->extraCostRepository->findById($extraCost, $companyId);

        abort_if(! $extraCostModel, 404);

        $this->extraCostService->update($extraCostModel, $request->validated(), $companyId);

        return redirect()
            ->route('recipes.show', $extraCostModel->recipe_id)
            ->with('success', 'Custo extra atualizado com sucesso.');
    }

    public function destroy(Request $request, int $extraCost): RedirectResponse
    {
        $companyId = (int) $request->user()->company_id;
        $extraCostModel = $this->extraCostRepository->findById($extraCost, $companyId);

        abort_if(! $extraCostModel, 404);

        $recipeId = (int) $extraCostModel->recipe_id;
        $this->extraCostService->delete($extraCostModel, $companyId);

        return redirect()
            ->route('recipes.show', $recipeId)
            ->with('success', 'Custo extra removido com sucesso.');
    }
}
