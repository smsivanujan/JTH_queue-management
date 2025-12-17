<?php

namespace App\Helpers;

use App\Models\Tenant;

class TenantHelper
{
    /**
     * Get the current tenant from the application container
     */
    public static function current(): ?Tenant
    {
        return app()->bound('tenant') ? app('tenant') : null;
    }

    /**
     * Get the current tenant ID
     */
    public static function currentId(): ?int
    {
        return app()->bound('tenant_id') ? app('tenant_id') : null;
    }

    /**
     * Check if a tenant is currently set
     */
    public static function hasTenant(): bool
    {
        return app()->bound('tenant') && app('tenant') !== null;
    }

    /**
     * Set the current tenant
     */
    public static function setTenant(Tenant $tenant): void
    {
        app()->instance('tenant', $tenant);
        app()->instance('tenant_id', $tenant->id);
    }

    /**
     * Clear the current tenant
     */
    public static function clearTenant(): void
    {
        app()->forgetInstance('tenant');
        app()->forgetInstance('tenant_id');
    }
}

