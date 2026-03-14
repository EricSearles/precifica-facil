<?php

namespace App\Services\Billing;

use App\Models\Billing\BillingInvoice;
use App\Models\Billing\BillingPayment;
use App\Models\Billing\BillingWebhookEvent;
use App\Services\Billing\Gateway\BillingGatewayInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BillingWebhookService
{
    public function __construct(
        private BillingGatewayInterface $gateway,
    ) {
    }

    public function handleProviderWebhook(string $provider, array $payload, bool $signatureValid): void
    {
        if (!$this->billingTablesReady()) {
            Log::warning('Webhook de billing recebido antes da criacao das tabelas.', [
                'provider' => $provider,
                'signature_valid' => $signatureValid,
            ]);

            return;
        }

        $parsed = $this->gateway->parseWebhook($payload);
        $event = BillingWebhookEvent::query()->firstOrNew([
            'provider' => $provider,
            'event_id' => (string) ($parsed['event_id'] ?? ''),
        ]);

        $invoice = null;
        if (!empty($parsed['billing_invoice_id'])) {
            $invoice = BillingInvoice::query()->find((int) $parsed['billing_invoice_id']);
        }

        $event->fill([
            'company_id' => $invoice?->company_id,
            'event_type' => $parsed['event_type'] ?? null,
            'resource_type' => $parsed['resource_type'] ?? null,
            'resource_id' => $parsed['resource_id'] ?? null,
            'signature_valid' => $signatureValid,
            'provider_status' => $parsed['provider_status'] ?? null,
            'local_status' => $parsed['local_status'] ?? null,
            'payload' => $payload,
            'processed_at' => now(),
            'response_payload' => $parsed,
        ]);
        $event->save();

        if (!$signatureValid || !$invoice) {
            return;
        }

        $invoice->status = $parsed['local_status'] ?? $invoice->status;
        $invoice->provider_status = $parsed['provider_status'] ?? $invoice->provider_status;
        $invoice->provider_invoice_id = $parsed['provider_invoice_id'] ?? $invoice->provider_invoice_id;
        $invoice->paid_at = $parsed['paid_at'] ?? $invoice->paid_at;
        $invoice->save();

        if (!empty($parsed['provider_payment_id'])) {
            BillingPayment::query()->updateOrCreate(
                [
                    'company_id' => $invoice->company_id,
                    'provider' => $provider,
                    'provider_payment_id' => $parsed['provider_payment_id'],
                ],
                [
                    'billing_customer_id' => $invoice->billing_customer_id,
                    'billing_invoice_id' => $invoice->id,
                    'status' => $parsed['local_status'] ?? 'pending',
                    'method' => $parsed['method'] ?? $invoice->billing_method,
                    'amount_cents' => $parsed['amount_cents'] ?? $invoice->total_cents,
                    'paid_at' => $parsed['paid_at'] ?? null,
                    'raw_payload' => $parsed['raw'] ?? $payload,
                ],
            );
        }
    }

    private function billingTablesReady(): bool
    {
        return Schema::hasTable('billing_webhook_events')
            && Schema::hasTable('billing_invoices')
            && Schema::hasTable('billing_payments');
    }
}
