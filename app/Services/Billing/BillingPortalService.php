<?php

namespace App\Services\Billing;

use App\Models\Billing\BillingPlan;
use App\Models\Billing\BillingCustomer;
use App\Models\Billing\BillingInvoice;
use App\Models\Billing\BillingSubscription;
use App\Models\Company;
use App\Services\Billing\Gateway\BillingGatewayInterface;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class BillingPortalService
{
    public function __construct(
        private BillingGatewayInterface $gateway,
        private PlanCapabilityService $planCapabilityService,
    ) {
    }

    public function buildPortalData(Company $company): array
    {
        $setupReady = $this->billingTablesExist();

        $summary = [
            'plan_name' => $this->formatPlanName((string) $company->plan),
            'company_status' => $this->formatCompanyStatus((string) $company->status),
            'trial_ends_at' => $company->trial_ends_at,
            'setup_ready' => $setupReady,
            'asaas_configured' => $this->asaasConfigured(),
        ];
        $resolvedPlan = $setupReady ? $this->planCapabilityService->resolvePlanForCompany($company) : null;

        if (!$setupReady) {
            return [
                'summary' => $summary,
                'resolved_plan' => null,
                'plan_snapshot' => null,
                'customer' => null,
                'subscription' => null,
                'next_invoice' => null,
                'open_invoices' => collect(),
                'recent_invoices' => collect(),
            ];
        }

        $customer = BillingCustomer::query()->first();
        $subscription = BillingSubscription::query()
            ->with('plan')
            ->latest('id')
            ->first();
        $openInvoices = BillingInvoice::query()->open()->orderBy('due_at')->limit(6)->get();
        $recentInvoices = BillingInvoice::query()->orderByDesc('due_at')->limit(10)->get();

        return [
            'summary' => $summary,
            'resolved_plan' => $resolvedPlan,
            'plan_snapshot' => $this->planCapabilityService->usageSnapshot($company),
            'customer' => $customer,
            'subscription' => $subscription,
            'next_invoice' => $openInvoices->first(),
            'open_invoices' => $openInvoices,
            'recent_invoices' => $recentInvoices,
        ];
    }

    public function prepareCompanyBilling(Company $company, string $method = 'boleto'): array
    {
        $this->assertBillingReady($company);

        $plan = $this->ensurePlan($company);
        $customer = $this->ensureCustomer($company);
        $subscription = $this->ensureSubscription($company, $plan, $customer, $method);

        return compact('plan', 'customer', 'subscription');
    }

    public function changeCompanyPlan(Company $company, string $planCode): BillingPlan
    {
        $normalizedPlanCode = $this->planCapabilityService->normalizePlanCode($planCode);
        $company->plan = $normalizedPlanCode;
        $company->save();

        $plan = $this->ensurePlan($company);
        $subscription = BillingSubscription::query()->latest('id')->first();

        if ($subscription) {
            $subscription->billing_plan_id = $plan->id;
            $subscription->amount_cents = $plan->price_cents;
            $subscription->save();
        }

        return $plan;
    }

    public function generateCompanyCharge(Company $company, string $method = 'boleto'): BillingInvoice
    {
        $billing = $this->prepareCompanyBilling($company, $method);

        if ((int) $billing['subscription']->amount_cents <= 0) {
            throw new RuntimeException('O plano atual não possui valor de cobrança configurado.');
        }

        $existingOpenInvoice = BillingInvoice::query()
            ->where('billing_subscription_id', $billing['subscription']->id)
            ->where('billing_method', $method)
            ->open()
            ->orderBy('due_at')
            ->first();

        if ($existingOpenInvoice) {
            if ($this->invoiceHasPaymentAccess($existingOpenInvoice)) {
                return $existingOpenInvoice;
            }

            return $this->processInvoiceCharge($existingOpenInvoice, $billing['customer'], $method, $billing['subscription']);
        }

        $invoice = BillingInvoice::query()->create([
            'company_id' => $company->id,
            'billing_customer_id' => $billing['customer']->id,
            'billing_subscription_id' => $billing['subscription']->id,
            'provider' => 'asaas',
            'number' => $this->generateInvoiceNumber($company),
            'status' => 'pending',
            'billing_method' => $method,
            'currency' => 'BRL',
            'amount_cents' => $billing['subscription']->amount_cents,
            'discount_cents' => 0,
            'total_cents' => $billing['subscription']->amount_cents,
            'due_at' => now()->addDays(3),
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
            'metadata' => [
                'source' => 'billing_portal',
            ],
        ]);

        return $this->processInvoiceCharge($invoice, $billing['customer'], $method, $billing['subscription']);
    }

    public function processExistingInvoice(BillingInvoice $invoice): BillingInvoice
    {
        $company = $invoice->company()->firstOrFail();
        $billing = $this->prepareCompanyBilling($company, $invoice->billing_method ?: 'boleto');

        return $this->processInvoiceCharge(
            $invoice,
            $billing['customer'],
            $invoice->billing_method ?: 'boleto',
            $billing['subscription'],
        );
    }

    public function invoiceHasPaymentAccess(BillingInvoice $invoice): bool
    {
        return filled($invoice->invoice_url)
            || filled($invoice->boleto_pdf_url)
            || filled($invoice->boleto_line)
            || filled($invoice->pix_copy_paste)
            || filled($invoice->pix_qr_code);
    }

    private function processInvoiceCharge(
        BillingInvoice $invoice,
        BillingCustomer $customer,
        string $method,
        BillingSubscription $subscription,
    ): BillingInvoice {
        try {
            $charge = $this->gateway->createChargeFromInvoice($invoice, $customer, $method);
            $payment = (array) ($charge['payment'] ?? []);
            $paymentId = (string) ($payment['id'] ?? '');
            $pix = (array) ($charge['pix'] ?? []);
        } catch (\Throwable $exception) {
            $invoice->metadata = array_filter([
                ...((array) $invoice->metadata),
                'last_error' => $exception->getMessage(),
            ]);
            $invoice->save();

            throw $exception;
        }

        if ($paymentId !== '') {
            try {
                $paymentDetails = $this->gateway->getPaymentDetails($paymentId);
                if (is_array($paymentDetails) && $paymentDetails !== []) {
                    $payment = array_merge($payment, $paymentDetails);
                }
            } catch (\Throwable) {
                // Mantem o retorno original caso a consulta complementar falhe.
            }
        }

        $boletoData = [];
        if ($method === 'boleto' && $paymentId !== '') {
            try {
                $boletoData = $this->gateway->getBoletoIdentificationField($paymentId);
            } catch (\Throwable) {
                $boletoData = [];
            }
        }

        $invoice->provider_invoice_id = (string) ($payment['id'] ?? $invoice->provider_invoice_id);
        $invoice->provider_status = (string) ($payment['status'] ?? $invoice->provider_status);
        $invoice->invoice_url = $payment['invoiceUrl'] ?? $payment['bankSlipUrl'] ?? $payment['paymentLink'] ?? $invoice->invoice_url;
        $invoice->boleto_line = $boletoData['identificationField'] ?? $payment['identificationField'] ?? $invoice->boleto_line;
        $invoice->boleto_pdf_url = $payment['bankSlipUrl'] ?? $invoice->boleto_pdf_url;
        $invoice->pix_qr_code = $pix['encodedImage'] ?? $invoice->pix_qr_code;
        $invoice->pix_copy_paste = $pix['payload'] ?? $invoice->pix_copy_paste;
        $invoice->metadata = array_filter([
            'source' => 'billing_portal',
            'asaas_payment' => $payment,
            'asaas_pix' => $pix,
            'asaas_boleto' => $boletoData,
            'last_error' => null,
        ]);
        $invoice->save();

        $subscription->next_due_at = $invoice->due_at;
        $subscription->save();

        return $invoice;
    }

    public function formatInvoiceStatus(string $status): string
    {
        return match ($status) {
            'paid' => 'Paga',
            'overdue' => 'Vencida',
            'canceled' => 'Cancelada',
            'refunded' => 'Estornada',
            default => 'Pendente',
        };
    }

    public function formatBillingMethod(?string $method): string
    {
        return match ((string) $method) {
            'pix' => 'Pix',
            'card' => 'Cartão',
            default => 'Boleto',
        };
    }

    public function formatSubscriptionStatus(?string $status): string
    {
        return match ((string) $status) {
            'active' => 'Ativa',
            'trialing' => 'Em teste',
            'canceled' => 'Cancelada',
            'past_due' => 'Em atraso',
            default => 'Não iniciada',
        };
    }

    private function billingTablesExist(): bool
    {
        foreach ([
            'billing_customers',
            'billing_plans',
            'billing_subscriptions',
            'billing_invoices',
            'billing_payments',
            'billing_webhook_events',
        ] as $table) {
            if (!Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    private function asaasConfigured(): bool
    {
        return (string) config('services.asaas.api_key') !== '';
    }

    private function ensurePlan(Company $company): BillingPlan
    {
        $planCode = $this->planCapabilityService->normalizePlanCode((string) $company->plan);

        return BillingPlan::query()->where('code', $planCode)->firstOr(function () use ($company, $planCode) {
            return BillingPlan::query()->create([
                'code' => $planCode,
                'name' => $this->formatPlanName($planCode),
                'description' => 'Plano padrao gerado automaticamente para a empresa.',
                'price_cents' => $this->resolvePlanPrice($planCode),
                'currency' => 'BRL',
                'billing_cycle' => 'monthly',
                'trial_days' => $company->plan === 'trial' ? max(0, $company->trialDaysLeft()) : 0,
                'is_active' => true,
                'metadata' => [
                    'generated' => true,
                    'limits' => [],
                    'features' => [],
                ],
            ]);
        });
    }

    private function ensureCustomer(Company $company): BillingCustomer
    {
        $customer = BillingCustomer::query()->firstOrCreate(
            ['company_id' => $company->id],
            [
                'provider' => 'asaas',
                'name' => $company->name,
                'email' => $company->email,
                'billing_email' => $company->email,
                'phone' => $company->phone,
                'mobile_phone' => $company->mobile_phone,
                'document_type' => $company->document_type,
                'document' => $company->document,
                'status' => 'active',
            ],
        );

        $customer->fill([
            'name' => $company->name,
            'email' => $company->email,
            'billing_email' => $customer->billing_email ?: $company->email,
            'phone' => $company->phone,
            'mobile_phone' => $company->mobile_phone,
            'document_type' => $company->document_type,
            'document' => $company->document,
            'status' => 'active',
        ]);
        $customer->save();

        if ($this->asaasConfigured()) {
            $providerCustomer = $this->gateway->createOrUpdateCustomer($customer);
            $customer->provider = 'asaas';
            $customer->provider_customer_id = (string) ($providerCustomer['id'] ?? $customer->provider_customer_id);
            $customer->metadata = array_filter([
                'asaas_customer' => $providerCustomer,
            ]);
            $customer->save();
        }

        return $customer;
    }

    private function ensureSubscription(Company $company, BillingPlan $plan, BillingCustomer $customer, string $method): BillingSubscription
    {
        $subscription = BillingSubscription::query()->firstOrCreate(
            ['company_id' => $company->id],
            [
                'billing_customer_id' => $customer->id,
                'billing_plan_id' => $plan->id,
                'provider' => 'asaas',
                'status' => $company->onTrial() ? 'trialing' : 'active',
                'billing_method' => $method,
                'amount_cents' => $plan->price_cents,
                'currency' => 'BRL',
                'started_at' => now(),
                'trial_ends_at' => $company->trial_ends_at,
                'metadata' => [
                    'generated' => true,
                ],
            ],
        );

        $subscription->fill([
            'billing_customer_id' => $customer->id,
            'billing_plan_id' => $plan->id,
            'status' => $company->onTrial() ? 'trialing' : 'active',
            'billing_method' => $method,
            'amount_cents' => $plan->price_cents,
            'currency' => 'BRL',
            'started_at' => $subscription->started_at ?: now(),
            'trial_ends_at' => $company->trial_ends_at,
        ]);
        $subscription->save();

        return $subscription;
    }

    private function resolvePlanPrice(string $planCode): int
    {
        return match ($planCode) {
            BillingPlan::CODE_BUSINESS => 7990,
            BillingPlan::CODE_PROFESSIONAL => 2590,
            default => 1490,
        };
    }

    private function generateInvoiceNumber(Company $company): string
    {
        $sequence = BillingInvoice::query()->count() + 1;

        return 'PF-' . str_pad((string) $company->id, 4, '0', STR_PAD_LEFT) . '-' . str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
    }

    private function assertBillingReady(Company $company): void
    {
        if (!$this->billingTablesExist()) {
            throw new RuntimeException('As tabelas de billing ainda não foram criadas.');
        }

        if (!$this->asaasConfigured()) {
            throw new RuntimeException('Asaas não configurado no ambiente.');
        }

        if (!$company->email) {
            throw new RuntimeException('Preencha o email da empresa no perfil antes de usar a cobrança.');
        }

        if (!$this->isValidDocument($company->document)) {
            throw new RuntimeException('Preencha um CPF ou CNPJ válido da empresa no perfil para gerar cobranças na Asaas.');
        }

        if (!$this->isValidPhone($company->mobile_phone, true)) {
            throw new RuntimeException('Preencha um celular válido da empresa no perfil. A Asaas exige esse campo para cobrança.');
        }

        if ($company->phone && !$this->isValidPhone($company->phone, false)) {
            throw new RuntimeException('O telefone da empresa está inválido. Corrija o campo no perfil para continuar.');
        }
    }

    private function formatPlanName(string $plan): string
    {
        return match ($plan) {
            'trial' => 'Teste grátis',
            'free', BillingPlan::CODE_STARTER => 'Plano Iniciante',
            BillingPlan::CODE_PROFESSIONAL => 'Plano Profissional',
            BillingPlan::CODE_BUSINESS => 'Plano Negócio',
            default => ucfirst(str_replace(['_', '-'], ' ', $plan)),
        };
    }

    private function formatCompanyStatus(string $status): string
    {
        return match ($status) {
            'active' => 'Ativa',
            'suspended' => 'Suspensa',
            'inactive' => 'Inativa',
            default => ucfirst($status),
        };
    }

    private function isValidPhone(?string $value, bool $mobile): bool
    {
        if ($value === null || trim($value) === '') {
            return false;
        }

        $digits = preg_replace('/\D+/', '', $value) ?: '';
        $length = strlen($digits);

        if ($mobile) {
            return $length === 11;
        }

        return in_array($length, [10, 11], true);
    }

    private function isValidDocument(?string $value): bool
    {
        if ($value === null || trim($value) === '') {
            return false;
        }

        $digits = preg_replace('/\D+/', '', $value) ?: '';

        return in_array(strlen($digits), [11, 14], true);
    }
}
