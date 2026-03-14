<?php

namespace App\Models;

use App\Models\Billing\BillingCustomer;
use App\Models\Billing\BillingInvoice;
use App\Models\Billing\BillingPayment;
use App\Models\Billing\BillingSubscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class Company extends Model
{
    public const TRIAL_GRACE_DAYS = 2;

    public const BILLING_GRACE_DAYS = 7;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'mobile_phone',
        'document',
        'document_type',
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

    public function billingCustomer(): HasOne
    {
        return $this->hasOne(BillingCustomer::class);
    }

    public function billingSubscriptions(): HasMany
    {
        return $this->hasMany(BillingSubscription::class);
    }

    public function billingInvoices(): HasMany
    {
        return $this->hasMany(BillingInvoice::class);
    }

    public function billingPayments(): HasMany
    {
        return $this->hasMany(BillingPayment::class);
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

    public function trialGraceEndsAt(): ?Carbon
    {
        if (! $this->trial_ends_at instanceof Carbon) {
            return null;
        }

        return $this->trial_ends_at->copy()->addDays(self::TRIAL_GRACE_DAYS)->endOfDay();
    }

    public function onTrialGracePeriod(): bool
    {
        $graceEndsAt = $this->trialGraceEndsAt();

        return $this->trialExpired()
            && $graceEndsAt instanceof Carbon
            && now()->lessThanOrEqualTo($graceEndsAt);
    }

    public function trialBlocked(): bool
    {
        $graceEndsAt = $this->trialGraceEndsAt();

        return $this->trialExpired()
            && $graceEndsAt instanceof Carbon
            && now()->greaterThan($graceEndsAt);
    }

    public function trialDaysLeft(): int
    {
        if (! $this->trial_ends_at instanceof Carbon) {
            return 0;
        }

        return max(0, now()->startOfDay()->diffInDays($this->trial_ends_at->copy()->startOfDay(), false));
    }

    public function latestDelinquentInvoice(): ?BillingInvoice
    {
        if (! Schema::hasTable('billing_invoices')) {
            return null;
        }

        return $this->billingInvoices()
            ->open()
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->orderBy('due_at')
            ->first();
    }

    public function billingGraceEndsAt(): ?Carbon
    {
        $invoice = $this->latestDelinquentInvoice();

        if (! $invoice?->due_at instanceof Carbon) {
            return null;
        }

        return $invoice->due_at->copy()->addDays(self::BILLING_GRACE_DAYS)->endOfDay();
    }

    public function onBillingGracePeriod(): bool
    {
        $graceEndsAt = $this->billingGraceEndsAt();

        return $this->latestDelinquentInvoice() instanceof BillingInvoice
            && $graceEndsAt instanceof Carbon
            && now()->lessThanOrEqualTo($graceEndsAt);
    }

    public function billingBlocked(): bool
    {
        $graceEndsAt = $this->billingGraceEndsAt();

        return $this->latestDelinquentInvoice() instanceof BillingInvoice
            && $graceEndsAt instanceof Carbon
            && now()->greaterThan($graceEndsAt);
    }

    public function accessBlocked(): bool
    {
        return $this->trialBlocked() || $this->billingBlocked();
    }

    public function accessNotice(): ?array
    {
        $delinquentInvoice = $this->latestDelinquentInvoice();
        $billingGraceEndsAt = $delinquentInvoice?->due_at instanceof Carbon
            ? $delinquentInvoice->due_at->copy()->addDays(self::BILLING_GRACE_DAYS)->endOfDay()
            : null;

        if ($this->billingBlocked()) {
            return [
                'level' => 'danger',
                'label' => 'Conta bloqueada',
                'message' => 'Existe cobrança vencida há mais de 7 dias. Regularize em Meu plano para liberar o restante do sistema.',
                'blocked' => true,
            ];
        }

        if ($this->trialBlocked()) {
            return [
                'level' => 'danger',
                'label' => 'Teste encerrado',
                'message' => 'O período de teste terminou e a carência de 2 dias foi encerrada. Escolha um plano em Meu plano para continuar usando o sistema.',
                'blocked' => true,
            ];
        }

        if ($delinquentInvoice && $billingGraceEndsAt instanceof Carbon) {
            return [
                'level' => 'warning',
                'label' => 'Pagamento em atraso',
                'message' => 'A conta será bloqueada em ' . $billingGraceEndsAt->format('d/m/Y') . ' se o pagamento não for identificado.',
                'blocked' => false,
            ];
        }

        if ($this->onTrialGracePeriod()) {
            return [
                'level' => 'warning',
                'label' => 'Teste encerrado',
                'message' => 'A conta será bloqueada em ' . $this->trialGraceEndsAt()?->format('d/m/Y') . ' se nenhum plano for regularizado.',
                'blocked' => false,
            ];
        }

        if ($this->onTrial()) {
            return [
                'level' => 'info',
                'label' => 'Período de teste',
                'message' => 'Restam ' . $this->trialDaysLeft() . ' dia(s) no período de teste.',
                'blocked' => false,
            ];
        }

        return null;
    }
}
