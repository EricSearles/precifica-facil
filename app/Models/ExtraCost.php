<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtraCost extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'product_id',
        'recipe_id',
        'description',
        'type',
        'value',
        'labor_minutes',
        'labor_hourly_rate',
        'monthly_salary',
        'monthly_hours',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'labor_minutes' => 'integer',
        'labor_hourly_rate' => 'decimal:2',
        'monthly_salary' => 'decimal:2',
        'monthly_hours' => 'integer',
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
