<?php

namespace App\Models\Billing;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingPayment extends Model
{
    use BelongsToCompany;

    protected $table = 'billing_payments';

    protected $fillable = [
        'company_id',
        'billing_customer_id',
        'billing_invoice_id',
        'provider',
        'provider_payment_id',
        'status',
        'method',
        'amount_cents',
        'paid_at',
        'raw_payload',
        'metadata',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'paid_at' => 'datetime',
        'raw_payload' => 'array',
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

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(BillingInvoice::class, 'billing_invoice_id');
    }
}
