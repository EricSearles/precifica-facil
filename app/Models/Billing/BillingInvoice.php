<?php

namespace App\Models\Billing;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingInvoice extends Model
{
    use BelongsToCompany;

    protected $table = 'billing_invoices';

    protected $fillable = [
        'company_id',
        'billing_customer_id',
        'billing_subscription_id',
        'provider',
        'provider_invoice_id',
        'number',
        'status',
        'billing_method',
        'currency',
        'amount_cents',
        'discount_cents',
        'total_cents',
        'due_at',
        'paid_at',
        'period_start',
        'period_end',
        'provider_status',
        'invoice_url',
        'boleto_line',
        'boleto_pdf_url',
        'pix_qr_code',
        'pix_copy_paste',
        'canceled_at',
        'cancel_reason',
        'metadata',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'discount_cents' => 'integer',
        'total_cents' => 'integer',
        'due_at' => 'datetime',
        'paid_at' => 'datetime',
        'period_start' => 'date',
        'period_end' => 'date',
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

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(BillingSubscription::class, 'billing_subscription_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(BillingPayment::class, 'billing_invoice_id');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', ['pending', 'overdue']);
    }
}
