<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Services\Billing\BillingPortalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class BillingPortalController extends Controller
{
    public function __construct(
        private BillingPortalService $billingPortalService,
    ) {
    }

    public function show(Request $request): View
    {
        return view('billing.portal', $this->billingPortalService->buildPortalData(
            $request->user()->company()->firstOrFail(),
        ));
    }

    public function prepare(Request $request): RedirectResponse
    {
        $company = $request->user()->company()->firstOrFail();
        $method = $this->resolveMethod($request);

        try {
            $this->billingPortalService->prepareCompanyBilling($company, $method);
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('billing.portal')
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('billing.portal')
            ->with('success', 'Estrutura de cobrança preparada com sucesso.');
    }

    public function generateCharge(Request $request): RedirectResponse
    {
        $company = $request->user()->company()->firstOrFail();
        $method = $this->resolveMethod($request);

        try {
            $invoice = $this->billingPortalService->generateCompanyCharge($company, $method);
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('billing.portal')
                ->with('error', $exception->getMessage());
        }

        $message = 'Cobrança gerada com sucesso.';

        if ($invoice->billing_method === 'pix' && $invoice->pix_copy_paste) {
            $message = 'Cobrança Pix gerada com sucesso.';
        } elseif ($invoice->billing_method === 'boleto' && $invoice->boleto_pdf_url) {
            $message = 'Boleto gerado com sucesso.';
        } elseif ($invoice->billing_method === 'card' && $invoice->invoice_url) {
            $message = 'Link de pagamento por cartão gerado com sucesso.';
        }

        return redirect()
            ->route('billing.invoices.show', $invoice)
            ->with('success', $message);
    }

    public function changePlan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plan' => ['required', 'in:starter,professional,business'],
        ]);

        $company = $request->user()->company()->firstOrFail();
        $plan = $this->billingPortalService->changeCompanyPlan($company, $validated['plan']);

        return redirect()
            ->route('billing.portal')
            ->with('success', 'Plano alterado para ' . $plan->name . ' com sucesso.');
    }

    private function resolveMethod(Request $request): string
    {
        $method = strtolower((string) $request->input('method', 'boleto'));

        return in_array($method, ['boleto', 'pix', 'card'], true) ? $method : 'boleto';
    }
}
