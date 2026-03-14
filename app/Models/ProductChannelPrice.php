<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductChannelPrice extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'product_id',
        'sales_channel_id',
        'reference_price',
        'desired_net_value',
        'percentage_fee_total',
        'fixed_fee_total',
        'fee_total',
        'channel_price',
        'net_value',
    ];

    protected $casts = [
        'reference_price' => 'decimal:2',
        'desired_net_value' => 'decimal:2',
        'percentage_fee_total' => 'decimal:2',
        'fixed_fee_total' => 'decimal:2',
        'fee_total' => 'decimal:2',
        'channel_price' => 'decimal:2',
        'net_value' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function salesChannel(): BelongsTo
    {
        return $this->belongsTo(SalesChannel::class);
    }
}
