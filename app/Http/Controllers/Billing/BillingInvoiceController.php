<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Billing\BillingInvoice;
use App\Services\Billing\BillingPortalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class BillingInvoiceController extends Controller
{
    public function __construct(
        private BillingPortalService $billingPortalService,
    ) {
    }

    public function show(Request $request, BillingInvoice $billingInvoice): View
    {
        return view('billing.show', [
            'invoice' => $billingInvoice,
            'company' => $request->user()->company()->firstOrFail(),
            'billingPortalService' => $this->billingPortalService,
        ]);
    }

    public function process(BillingInvoice $billingInvoice): RedirectResponse
    {
        try {
            $invoice = $this->billingPortalService->processExistingInvoice($billingInvoice);
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('billing.invoices.show', $billingInvoice)
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('billing.invoices.show', $invoice)
            ->with('success', 'Cobranca processada com sucesso.');
    }
}
