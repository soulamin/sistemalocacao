<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::creating(function ($model): void {
            if (! auth()->check()) {
                return;
            }

            if (! $model->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder): void {
            if (! auth()->check()) {
                return;
            }

            $tenantId = auth()->user()->tenant_id;

            if ($tenantId) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', $tenantId);
            }
        });
    }
}
