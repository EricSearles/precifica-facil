<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'category_id',
        'name',
        'sale_unit',
        'yield_quantity',
        'profit_margin_type',
        'profit_margin_value',
        'use_global_margin',
        'calculated_unit_cost',
        'suggested_sale_price',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'yield_quantity' => 'decimal:2',
        'profit_margin_value' => 'decimal:2',
        'use_global_margin' => 'boolean',
        'calculated_unit_cost' => 'decimal:2',
        'suggested_sale_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function extraCosts(): HasMany
    {
        return $this->hasMany(ExtraCost::class);
    }

    public function productPackagings(): HasMany
    {
        return $this->hasMany(ProductPackaging::class);
    }

    public function productChannelPrices(): HasMany
    {
        return $this->hasMany(ProductChannelPrice::class);
    }
}
