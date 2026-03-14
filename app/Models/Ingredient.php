<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'brand',
        'purchase_unit',
        'purchase_quantity',
        'purchase_price',
        'content_quantity',
        'content_unit',
        'base_unit',
        'base_quantity',
        'unit_cost',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'purchase_quantity' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'content_quantity' => 'decimal:3',
        'base_quantity' => 'decimal:2',
        'unit_cost' => 'decimal:6',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function recipeItems(): HasMany
    {
        return $this->hasMany(RecipeItem::class);
    }
}
