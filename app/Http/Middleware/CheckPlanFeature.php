<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanFeature
{
    /**
     * Handle an incoming request.
     *
     * Checks if the tenant's subscription plan has access to a specific feature
     * Super Admins bypass all feature checks
     * 
     * Usage: middleware('plan.feature:analytics,api_access')
     */
    public function handle(Request $request, Closure $next, string ...$features): Response
    {
        // Super Admin bypass: Platform admins have access to all features
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return $next($request);
        }

        // Tenant is guaranteed to be set by IdentifyTenant middleware
        // This middleware runs AFTER tenant middleware, so tenant must exist
        $tenant = app()->bound('tenant') ? app('tenant') : null;

        if (!$tenant) {
            \Log::error('CheckPlanFeature: Tenant not set', [
                'user_id' => auth()->id(),
                'current_tenant_id' => auth()->user()?->current_tenant_id,
            ]);
            abort(500, 'System error: Tenant context not available');
        }

        // Check if tenant has active subscription
        $subscription = $tenant->subscription;
        
        if (!$subscription || !$subscription->isActive()) {
            return redirect()->route('subscription.required')
                ->with('error', 'An active subscription is required to access this feature.');
        }

        // Check if subscription has any of the required features
        $hasFeature = false;
        foreach ($features as $feature) {
            if ($subscription->hasFeature($feature)) {
                $hasFeature = true;
                break;
            }
        }

        if (!$hasFeature) {
            $featureList = implode(', ', $features);
            abort(403, "This feature ({$featureList}) is not available on your current plan. Please upgrade to access this feature.");
        }

        return $next($request);
    }
}

