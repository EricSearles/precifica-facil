<?php

namespace App\Models\Billing;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingCustomer extends Model
{
    use BelongsToCompany;
    use SoftDeletes;

    protected $table = 'billing_customers';

    protected $fillable = [
        'company_id',
        'provider',
        'provider_customer_id',
        'name',
        'document_type',
        'document',
        'email',
        'phone',
        'mobile_phone',
        'billing_email',
        'status',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(BillingSubscription::class, 'billing_customer_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(BillingInvoice::class, 'billing_customer_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(BillingPayment::class, 'billing_customer_id');
    }
}
