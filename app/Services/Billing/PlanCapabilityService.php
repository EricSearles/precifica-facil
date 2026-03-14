<?php

namespace App\Services\Billing;

use App\Models\Billing\BillingPlan;
use App\Models\Company;
use Illuminate\Support\Facades\Schema;

class PlanCapabilityService
{
    public function resolvePlanForCompany(Company $company): ?BillingPlan
    {
        if (!Schema::hasTable('billing_plans')) {
            return null;
        }

        $planCode = $this->normalizePlanCode((string) $company->plan);

        return BillingPlan::query()->where('code', $planCode)->first();
    }

    public function limitForCompany(Company $company, string $resource): ?int
    {
        return $this->resolvePlanForCompany($company)?->limitFor($resource);
    }

    public function featureEnabledForCompany(Company $company, string $feature): bool
    {
        return $this->resolvePlanForCompany($company)?->featureEnabled($feature) ?? false;
    }

    public function usageSnapshot(Company $company): array
    {
        $plan = $this->resolvePlanForCompany($company);

        return [
            'plan' => $plan?->code,
            'limits' => data_get($plan?->metadata, 'limits', []),
            'features' => data_get($plan?->metadata, 'features', []),
            'enforcement_enabled' => false,
        ];
    }

    public function normalizePlanCode(string $planCode): string
    {
        return match (strtolower(trim($planCode))) {
            BillingPlan::CODE_PROFESSIONAL, 'pro', 'profissional' => BillingPlan::CODE_PROFESSIONAL,
            BillingPlan::CODE_BUSINESS, 'negocio', 'business' => BillingPlan::CODE_BUSINESS,
            'trial', 'free', 'iniciante', BillingPlan::CODE_STARTER => BillingPlan::CODE_STARTER,
            default => BillingPlan::CODE_STARTER,
        };
    }
}
