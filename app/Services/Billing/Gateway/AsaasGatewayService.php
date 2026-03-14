<?php

namespace App\Services\Billing\Gateway;

use App\Models\Billing\BillingCustomer;
use App\Models\Billing\BillingInvoice;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AsaasGatewayService implements BillingGatewayInterface
{
    private string $baseUrl;

    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.asaas.base_url'), '/');
        $this->apiKey = (string) config('services.asaas.api_key');
    }

    public function createOrUpdateCustomer(BillingCustomer $customer): array
    {
        $externalReference = 'billing_customer:' . $customer->id;
        $payload = [
            'name' => $customer->name,
            'email' => $customer->billing_email ?: $customer->email,
            'cpfCnpj' => $customer->document ?: null,
            'phone' => $this->normalizePhone($customer->phone),
            'mobilePhone' => $this->normalizePhone($customer->mobile_phone),
            'externalReference' => $externalReference,
            'notificationDisabled' => false,
        ];

        $existing = null;

        if ($customer->provider_customer_id) {
            try {
                $existing = $this->request('get', '/customers/' . $customer->provider_customer_id);
            } catch (\Throwable) {
                $existing = null;
            }
        }

        if ($existing === null) {
            $existing = $this->findCustomerByExternalReference($externalReference);
        }

        if ($existing !== null) {
            $existingId = (string) ($existing['id'] ?? '');

            if ($existingId !== '') {
                return $this->request('put', '/customers/' . $existingId, $payload);
            }
        }

        return $this->request('post', '/customers', $payload);
    }

    public function createChargeFromInvoice(BillingInvoice $invoice, BillingCustomer $customer, string $method): array
    {
        $customerPayload = $this->createOrUpdateCustomer($customer);
        $asaasCustomerId = (string) ($customerPayload['id'] ?? '');

        if ($asaasCustomerId === '') {
            throw new RuntimeException('Asaas nao retornou o identificador do cliente.');
        }

        $billingType = $this->normalizeMethodToBillingType($method);
        $payment = $this->request('post', '/payments', [
            'customer' => $asaasCustomerId,
            'billingType' => $billingType,
            'value' => round(((int) $invoice->total_cents) / 100, 2),
            'dueDate' => optional($invoice->due_at)->format('Y-m-d') ?: now()->format('Y-m-d'),
            'description' => 'Fatura ' . ($invoice->number ?: '#' . $invoice->id),
            'externalReference' => 'billing_invoice:' . $invoice->id,
        ]);

        $paymentId = (string) ($payment['id'] ?? '');

        if ($paymentId === '') {
            throw new RuntimeException('Asaas nao retornou o identificador do pagamento.');
        }

        $pix = [];

        if ($billingType === 'PIX') {
            try {
                $pix = $this->request('get', '/payments/' . $paymentId . '/pixQrCode');
            } catch (\Throwable) {
                $pix = [];
            }
        }

        return [
            'payment' => $payment,
            'pix' => $pix,
        ];
    }

    public function getPaymentDetails(string $paymentId): array
    {
        return $this->request('get', '/payments/' . $paymentId);
    }

    public function getBoletoIdentificationField(string $paymentId): array
    {
        return $this->request('get', '/payments/' . $paymentId . '/identificationField');
    }

    public function parseWebhook(array $payload): array
    {
        $payment = (array) ($payload['payment'] ?? []);
        $status = strtoupper((string) ($payment['status'] ?? ''));
        $billingType = strtoupper((string) ($payment['billingType'] ?? ''));
        $paymentId = (string) ($payment['id'] ?? '');
        $externalReference = (string) ($payment['externalReference'] ?? '');
        $invoiceId = str_starts_with($externalReference, 'billing_invoice:')
            ? (int) str_replace('billing_invoice:', '', $externalReference)
            : null;

        return [
            'event_id' => (string) ($payload['id'] ?? ''),
            'event_type' => (string) ($payload['event'] ?? ''),
            'resource_type' => 'payment',
            'resource_id' => $paymentId,
            'provider_status' => $status,
            'provider_invoice_id' => $paymentId,
            'provider_payment_id' => $paymentId,
            'billing_invoice_id' => $invoiceId,
            'amount_cents' => (int) round(((float) ($payment['value'] ?? 0)) * 100),
            'paid_at' => $payment['paymentDate'] ?? $payment['clientPaymentDate'] ?? null,
            'method' => $this->normalizeBillingTypeToMethod($billingType),
            'local_status' => $this->mapProviderStatusToLocalStatus($status),
            'raw' => $payload,
        ];
    }

    private function findCustomerByExternalReference(string $externalReference): ?array
    {
        $response = $this->request('get', '/customers', [
            'externalReference' => $externalReference,
            'limit' => 1,
            'offset' => 0,
        ]);

        $rows = Arr::get($response, 'data', []);

        if (!is_array($rows) || $rows === []) {
            return null;
        }

        return (array) $rows[0];
    }

    private function request(string $method, string $path, array $payload = []): array
    {
        if ($this->baseUrl === '' || $this->apiKey === '') {
            throw new RuntimeException('Asaas nao configurado. Defina ASAAS_BASE_URL e ASAAS_API_KEY.');
        }

        $client = Http::timeout(30)
            ->acceptJson()
            ->withHeaders([
                'access_token' => $this->apiKey,
            ]);

        $response = match ($method) {
            'get' => $client->get($this->baseUrl . $path, $payload),
            'put' => $client->put($this->baseUrl . $path, $payload),
            default => $client->post($this->baseUrl . $path, $payload),
        };

        if (!$response->successful()) {
            throw new RuntimeException('Erro Asaas [' . $response->status() . ']: ' . $response->body());
        }

        $json = $response->json();

        if (!is_array($json)) {
            throw new RuntimeException('Resposta invalida do Asaas.');
        }

        return $json;
    }

    private function normalizeMethodToBillingType(string $method): string
    {
        return match (strtolower(trim($method))) {
            'pix' => 'PIX',
            'card', 'cartao', 'credito' => 'UNDEFINED',
            default => 'BOLETO',
        };
    }

    private function normalizeBillingTypeToMethod(string $billingType): string
    {
        return match ($billingType) {
            'PIX' => 'pix',
            'CREDIT_CARD', 'UNDEFINED' => 'card',
            default => 'boleto',
        };
    }

    private function mapProviderStatusToLocalStatus(string $providerStatus): string
    {
        return match ($providerStatus) {
            'RECEIVED', 'CONFIRMED', 'RECEIVED_IN_CASH' => 'paid',
            'OVERDUE' => 'overdue',
            'REFUNDED', 'PARTIALLY_REFUNDED' => 'refunded',
            'DELETED', 'CANCELED' => 'canceled',
            default => 'pending',
        };
    }

    private function normalizePhone(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $value) ?: '';

        return $digits !== '' ? $digits : null;
    }
}
