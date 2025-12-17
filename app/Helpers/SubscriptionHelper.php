<?php

namespace App\Helpers;

use App\Models\Tenant;
use App\Models\Plan;

class SubscriptionHelper
{
    /**
     * Check if current tenant has a specific feature
     * Super Admins always return true (all features enabled)
     */
    public static function hasFeature(string $feature): bool
    {
        // Super Admin bypass: Platform admins have access to all features
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return true;
        }

        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return false;
        }
        
        return $tenant->hasFeature($feature);
    }

    /**
     * Get current tenant's plan
     * Super Admins return null (they don't have plan restrictions)
     */
    public static function getCurrentPlan(): ?Plan
    {
        // Super Admin bypass: Platform admins don't have plan restrictions
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return null; // No plan for super admin
        }

        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return null;
        }
        
        return $tenant->getCurrentPlan();
    }

    /**
     * Check if current tenant can create more clinics
     * Super Admins always return true (unlimited)
     */
    public static function canCreateClinic(): bool
    {
        // Super Admin bypass: Platform admins can create unlimited clinics
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return true;
        }

        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return false;
        }
        
        $subscription = $tenant->subscription;
        
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }
        
        $plan = $subscription->plan;
        $maxClinics = $subscription->max_clinics ?? ($plan ? $plan->max_clinics : 10);
        
        // Unlimited
        if ($maxClinics === -1) {
            return true;
        }
        
        $currentCount = $tenant->clinics()->count();
        
        return $currentCount < $maxClinics;
    }

    /**
     * Check if current tenant can add more users
     * Super Admins always return true (unlimited)
     */
    public static function canAddUser(): bool
    {
        // Super Admin bypass: Platform admins can add unlimited users
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return true;
        }

        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return false;
        }
        
        $subscription = $tenant->subscription;
        
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }
        
        $plan = $subscription->plan;
        $maxUsers = $subscription->max_users ?? ($plan ? $plan->max_users : 5);
        
        // Unlimited
        if ($maxUsers === -1) {
            return true;
        }
        
        $currentCount = $tenant->users()->wherePivot('is_active', true)->count();
        
        return $currentCount < $maxUsers;
    }

    /**
     * Check if current tenant can open more screens
     * Super Admins always return true (unlimited)
     */
    public static function canOpenScreen(): bool
    {
        // Super Admin bypass: Platform admins can use unlimited screens
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return true;
        }

        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return false;
        }
        
        $subscription = $tenant->subscription;
        
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }
        
        $plan = $subscription->plan;
        $maxScreens = $subscription->max_screens ?? ($plan ? $plan->max_screens : 1);
        
        // Unlimited
        if ($maxScreens === -1) {
            return true;
        }
        
        // Primary: Use database-based tracking (new, reliable)
        $activeScreens = \App\Models\ActiveScreen::getActiveCount($tenant->id);
        
        // Fallback: Use session-based tracking (backward compatibility)
        if ($activeScreens === 0) {
            $activeScreens = session('active_screens', 0);
        }
        
        return $activeScreens < $maxScreens;
    }

    /**
     * Get maximum screens allowed for current tenant
     * Super Admins return -1 (unlimited)
     */
    public static function getMaxScreens(): int
    {
        // Super Admin bypass: Platform admins have unlimited screens
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return -1; // Unlimited
        }

        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return 0;
        }
        
        $subscription = $tenant->subscription;
        
        if (!$subscription || !$subscription->isActive()) {
            return 0;
        }
        
        $plan = $subscription->plan;
        
        return $subscription->max_screens ?? ($plan ? $plan->max_screens : 1);
    }
}

