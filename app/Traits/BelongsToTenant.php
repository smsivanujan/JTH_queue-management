<?php

namespace App\Traits;

use App\Models\Tenant;

trait BelongsToTenant
{
    /**
     * Get the tenant that owns this model
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope a query to only include records for the current tenant
     */
    public function scopeForCurrentTenant($query)
    {
        $tenantId = app()->bound('tenant_id') ? app('tenant_id') : null;
        
        if ($tenantId) {
            return $query->where('tenant_id', $tenantId);
        }
        
        return $query;
    }

    /**
     * Ensure tenant_id is set when creating
     */
    protected static function bootBelongsToTenant()
    {
        static::creating(function ($model) {
            $tenantId = app()->bound('tenant_id') ? app('tenant_id') : null;
            if (empty($model->tenant_id) && $tenantId) {
                $model->tenant_id = $tenantId;
            }
        });
    }
}

