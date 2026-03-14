<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $builder): void {
            $companyId = static::resolveCurrentCompanyId();

            if ($companyId === null) {
                return;
            }

            $builder->where($builder->getModel()->getTable().'.company_id', $companyId);
        });

        static::creating(function ($model): void {
            if (! empty($model->company_id)) {
                return;
            }

            $companyId = static::resolveCurrentCompanyId();

            if ($companyId !== null) {
                $model->company_id = $companyId;
            }
        });
    }

    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->withoutGlobalScope('company')
            ->where($this->getTable().'.company_id', $companyId);
    }

    public function scopeForCurrentCompany(Builder $query): Builder
    {
        $companyId = static::resolveCurrentCompanyId();

        if ($companyId === null) {
            return $query->withoutGlobalScope('company');
        }

        return $query->forCompany($companyId);
    }

    public function resolveRouteBindingQuery($query, $value, $field = null): Builder
    {
        if ($query instanceof Model) {
            $query = $query->newQuery();
        }

        $query->where($field ?? $this->getRouteKeyName(), $value);

        $companyId = static::resolveCurrentCompanyId();

        if ($companyId !== null) {
            $query->where($this->getTable().'.company_id', $companyId);
        }

        return $query;
    }

    protected static function resolveCurrentCompanyId(): ?int
    {
        if (app()->runningInConsole()) {
            return null;
        }

        $user = auth()->user();

        if (! $user || empty($user->company_id)) {
            return null;
        }

        return (int) $user->company_id;
    }
}
