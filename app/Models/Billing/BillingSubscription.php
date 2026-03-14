<?php

namespace App\Models\Billing;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingSubscription extends Model
{
    use BelongsToCompany;

    protected $table = 'billing_subscriptions';

    protected $fillable = [
        'company_id',
        'billing_customer_id',
        'billing_plan_id',
        'provider',
        'provider_subscription_id',
        'status',
        'billing_method',
        'amount_cents',
        'currency',
        'started_at',
        'trial_ends_at',
        'next_due_at',
        'canceled_at',
        'metadata',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'started_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'next_due_at' => 'datetime',
        'canceled_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(BillingCustomer::class, 'billing_customer_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(BillingPlan::class, 'billing_plan_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(BillingInvoice::class, 'billing_subscription_id');
    }
}
