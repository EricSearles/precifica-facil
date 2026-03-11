<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Company extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'plan',
        'status',
        'trial_ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function extraCosts(): HasMany
    {
        return $this->hasMany(ExtraCost::class);
    }

    public function packagings(): HasMany
    {
        return $this->hasMany(Packaging::class);
    }

    public function productPackagings(): HasMany
    {
        return $this->hasMany(ProductPackaging::class);
    }

    public function salesChannels(): HasMany
    {
        return $this->hasMany(SalesChannel::class);
    }

    public function salesChannelFees(): HasMany
    {
        return $this->hasMany(SalesChannelFee::class);
    }

    public function productChannelPrices(): HasMany
    {
        return $this->hasMany(ProductChannelPrice::class);
    }

    public function setting(): HasOne
    {
        return $this->hasOne(Setting::class);
    }

    public function onTrial(): bool
    {
        return $this->plan === 'trial'
            && $this->trial_ends_at instanceof Carbon
            && $this->trial_ends_at->isFuture();
    }

    public function trialExpired(): bool
    {
        return $this->plan === 'trial'
            && $this->trial_ends_at instanceof Carbon
            && $this->trial_ends_at->isPast();
    }

    public function trialDaysLeft(): int
    {
        if (! $this->trial_ends_at instanceof Carbon) {
            return 0;
        }

        return max(0, now()->startOfDay()->diffInDays($this->trial_ends_at->copy()->startOfDay(), false));
    }
}
