<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\UpdateSettingRequest;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        protected SettingService $settingService,
    ) {
    }

    public function edit(Request $request): View
    {
        $setting = $this->settingService->getOrCreateForCompany((int) $request->user()->company_id);

        return view('settings.edit', [
            'setting' => $setting,
        ]);
    }

    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        $this->settingService->updateForCompany((int) $request->user()->company_id, $request->validated());

        return redirect()
            ->route('settings.edit')
            ->with('success', 'Configurações atualizadas com sucesso.');
    }
}
