<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    protected $fillable = [
        'company_id',
        'default_profit_margin',
        'currency',
        'decimal_places',
    ];

    protected $casts = [
        'default_profit_margin' => 'decimal:2',
        'decimal_places' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}