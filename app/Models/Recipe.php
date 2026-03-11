<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $fillable = [
        'company_id',
        'product_id',
        'name',
        'yield_quantity',
        'yield_unit',
        'ingredients_cost_total',
        'extra_cost_total',
        'packaging_cost_total',
        'recipe_total_cost',
        'unit_cost',
        'suggested_sale_price',
        'preparation_method',
        'notes',
    ];

    protected $casts = [
        'yield_quantity' => 'decimal:2',
        'ingredients_cost_total' => 'decimal:2',
        'extra_cost_total' => 'decimal:2',
        'packaging_cost_total' => 'decimal:2',
        'recipe_total_cost' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'suggested_sale_price' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RecipeItem::class);
    }

    public function extraCosts(): HasMany
    {
        return $this->hasMany(ExtraCost::class);
    }
}
