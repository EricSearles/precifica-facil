<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtraCost extends Model
{
    protected $fillable = [
        'company_id',
        'product_id',
        'recipe_id',
        'description',
        'type',
        'value',
        'labor_minutes',
        'labor_hourly_rate',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'labor_minutes' => 'integer',
        'labor_hourly_rate' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
