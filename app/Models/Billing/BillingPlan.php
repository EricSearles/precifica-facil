<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingPlan extends Model
{
    protected $table = 'billing_plans';

    public const CODE_STARTER = 'starter';
    public const CODE_PROFESSIONAL = 'professional';
    public const CODE_BUSINESS = 'business';

    protected $fillable = [
        'code',
        'name',
        'description',
        'price_cents',
        'currency',
        'billing_cycle',
        'trial_days',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'price_cents' => 'integer',
        'trial_days' => 'integer',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(BillingSubscription::class, 'billing_plan_id');
    }

    public function featureEnabled(string $feature): bool
    {
        return (bool) data_get($this->metadata, 'features.' . $feature, false);
    }

    public function limitFor(string $resource): ?int
    {
        $limit = data_get($this->metadata, 'limits.' . $resource);

        return is_numeric($limit) ? (int) $limit : null;
    }
}
