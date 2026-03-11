<?php

namespace App\Http\Controllers;

use App\Http\Requests\Packagings\StorePackagingRequest;
use App\Http\Requests\Packagings\UpdatePackagingRequest;
use App\Repositories\PackagingRepository;
use App\Services\PackagingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackagingController extends Controller
{
    public function __construct(
        protected PackagingRepository $packagingRepository,
        protected PackagingService $packagingService,
    ) {
    }

    public function index(Request $request): View
    {
        $packagings = $this->packagingRepository->getByCompany((int) $request->user()->company_id);

        return view('packagings.index', [
            'packagings' => $packagings,
        ]);
    }

    public function create(): View
    {
        return view('packagings.create');
    }

    public function store(StorePackagingRequest $request): RedirectResponse
    {
        $this->packagingService->create($request->validated(), (int) $request->user()->company_id);

        return redirect()->route('packagings.index')->with('success', 'Embalagem criada com sucesso.');
    }

    public function edit(Request $request, int $packaging): View
    {
        $packagingModel = $this->packagingRepository->findById($packaging, (int) $request->user()->company_id);

        abort_if(! $packagingModel, 404);

        return view('packagings.edit', [
            'packaging' => $packagingModel,
        ]);
    }

    public function update(UpdatePackagingRequest $request, int $packaging): RedirectResponse
    {
        $packagingModel = $this->packagingRepository->findById($packaging, (int) $request->user()->company_id);

        abort_if(! $packagingModel, 404);

        $this->packagingService->update($packagingModel, $request->validated());

        return redirect()->route('packagings.index')->with('success', 'Embalagem atualizada com sucesso.');
    }

    public function destroy(Request $request, int $packaging): RedirectResponse
    {
        $packagingModel = $this->packagingRepository->findById($packaging, (int) $request->user()->company_id);

        abort_if(! $packagingModel, 404);

        $this->packagingService->delete($packagingModel);

        return redirect()->route('packagings.index')->with('success', 'Embalagem removida com sucesso.');
    }
}
