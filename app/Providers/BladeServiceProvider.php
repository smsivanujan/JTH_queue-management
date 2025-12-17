<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Check if user has role
        Blade::if('role', function ($roles) {
            if (!auth()->check()) {
                return false;
            }

            $user = auth()->user();
            $roles = is_array($roles) ? $roles : explode(',', $roles);
            $roles = array_map('trim', $roles);

            return $user->hasRole($roles);
        });

        // Check if user is admin
        Blade::if('admin', function () {
            if (!auth()->check()) {
                return false;
            }

            return auth()->user()->isAdmin();
        });

        // Check if user can manage queues
        Blade::if('canManageQueues', function () {
            if (!auth()->check()) {
                return false;
            }

            return auth()->user()->canManageQueues();
        });

        // Check if user can access lab
        Blade::if('canAccessLab', function () {
            if (!auth()->check()) {
                return false;
            }

            return auth()->user()->canAccessLab();
        });

        // Check if user is viewer (read-only)
        Blade::if('viewer', function () {
            if (!auth()->check()) {
                return false;
            }

            return auth()->user()->hasRole('viewer');
        });

        // Check if user is super admin (platform administrator)
        Blade::if('superAdmin', function () {
            if (!auth()->check()) {
                return false;
            }

            return auth()->user()->isSuperAdmin();
        });
    }
}

