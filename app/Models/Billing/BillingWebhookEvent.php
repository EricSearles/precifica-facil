<?php

namespace App\Models\Billing;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingWebhookEvent extends Model
{
    use BelongsToCompany;

    protected $table = 'billing_webhook_events';

    protected $fillable = [
        'company_id',
        'provider',
        'event_id',
        'event_type',
        'resource_type',
        'resource_id',
        'signature_valid',
        'provider_status',
        'local_status',
        'processed_at',
        'payload',
        'response_payload',
    ];

    protected $casts = [
        'signature_valid' => 'boolean',
        'processed_at' => 'datetime',
        'payload' => 'array',
        'response_payload' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
