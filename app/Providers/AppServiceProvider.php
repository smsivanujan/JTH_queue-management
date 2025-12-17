<?php

namespace App\Providers;

use App\Models\Tenant;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        // Register route model binding for tenant
        Route::bind('tenant', function ($value) {
            return Tenant::where('slug', $value)
                ->where('is_active', true)
                ->firstOrFail();
        });
        
        // Register route model binding for clinic (scoped by tenant)
        // NOTE: Route model binding runs BEFORE middleware, so tenant might not be set yet
        // We'll scope by tenant in the query if tenant is available, otherwise scope will happen in middleware
        Route::bind('clinic', function ($value) {
            $tenant = app()->bound('tenant') ? app('tenant') : null;
            
            // If tenant is available (middleware has run), scope by tenant
            if ($tenant) {
                return \App\Models\Clinic::where('id', $value)
                    ->where('tenant_id', $tenant->id)
                    ->firstOrFail();
            }
            
            // If tenant not available yet (middleware hasn't run), just find by ID
            // TenantScope will filter it, and middleware will verify access
            return \App\Models\Clinic::findOrFail($value);
        });

        // Register route model binding for staff (User model, scoped by tenant)
        // NOTE: Route model binding runs BEFORE middleware, so tenant might not be set yet
        Route::bind('staff', function ($value) {
            $tenant = app()->bound('tenant') ? app('tenant') : null;
            
            $user = \App\Models\User::findOrFail($value);
            
            // If tenant is available, verify access
            if ($tenant) {
                // Super Admin can access any user in their current tenant context
                // Regular users: Ensure user belongs to tenant
                if (auth()->check() && !auth()->user()->isSuperAdmin() && !$user->belongsToTenant($tenant->id)) {
                    abort(403, 'Unauthorized access to this staff member.');
                }
            }
            
            // Tenant verification will happen in middleware
            return $user;
        });

        // Register route model binding for service (scoped by tenant)
        // NOTE: Route model binding runs BEFORE middleware, so tenant might not be set yet
        Route::bind('service', function ($value) {
            $tenant = app()->bound('tenant') ? app('tenant') : null;
            
            // If tenant is available (middleware has run), scope by tenant
            if ($tenant) {
                return \App\Models\Service::where('id', $value)
                    ->where('tenant_id', $tenant->id)
                    ->firstOrFail();
            }
            
            // If tenant not available yet, just find by ID
            // TenantScope will filter it, and middleware will verify access
            return \App\Models\Service::findOrFail($value);
        });
    }
}
