<?php

namespace App\Services\Billing\Gateway;

use App\Models\Billing\BillingCustomer;
use App\Models\Billing\BillingInvoice;

interface BillingGatewayInterface
{
    public function createOrUpdateCustomer(BillingCustomer $customer): array;

    public function createChargeFromInvoice(BillingInvoice $invoice, BillingCustomer $customer, string $method): array;

    public function getPaymentDetails(string $paymentId): array;

    public function getBoletoIdentificationField(string $paymentId): array;

    public function parseWebhook(array $payload): array;
}
