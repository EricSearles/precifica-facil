<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeItem extends Model
{
    protected $fillable = [
        'company_id',
        'recipe_id',
        'ingredient_id',
        'quantity_used',
        'unit_used',
        'unit_cost_snapshot',
        'total_cost',
    ];

    protected $casts = [
        'quantity_used' => 'decimal:2',
        'unit_cost_snapshot' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
