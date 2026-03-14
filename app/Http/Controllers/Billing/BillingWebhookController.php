<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Services\Billing\BillingWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillingWebhookController extends Controller
{
    public function __construct(
        private BillingWebhookService $webhookService,
    ) {
    }

    public function handle(Request $request, string $provider): JsonResponse
    {
        $provider = strtolower(trim($provider));
        $configuredToken = (string) config('services.asaas.webhook_token');
        $receivedToken = (string) $request->header('asaas-access-token', '');
        $signatureValid = $configuredToken === '' || hash_equals($configuredToken, $receivedToken);

        $this->webhookService->handleProviderWebhook($provider, $request->all(), $signatureValid);

        if (!$signatureValid) {
            return response()->json(['ok' => false, 'message' => 'invalid signature'], 401);
        }

        return response()->json(['ok' => true]);
    }
}
