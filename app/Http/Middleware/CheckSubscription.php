<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * Checks if tenant has active subscription or is on trial
     * Super Admins bypass all subscription checks
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip subscription check for system-wide metrics dashboard (admin-only, doesn't require subscription)
        if ($request->routeIs('metrics.index')) {
            return $next($request);
        }

        // Super Admin bypass: Platform admins are not restricted by subscription plans
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return $next($request);
        }

        $tenant = $request->get('tenant') ?? (app()->bound('tenant') ? app('tenant') : null);

        if (!$tenant) {
            return $next($request);
        }

        // Check if tenant is active
        if (!$tenant->is_active) {
            abort(403, 'Your account has been deactivated. Please contact support.');
        }

        // Check subscription or trial
        $hasActiveSubscription = $tenant->hasActiveSubscription();
        $isOnTrial = $tenant->isOnTrial();

        if (!$hasActiveSubscription && !$isOnTrial) {
            // Redirect to subscription page
            return redirect()->route('subscription.required')
                ->with('error', 'Your subscription has expired. Please renew to continue.');
        }

        // Set subscription limits in request
        if ($hasActiveSubscription && $subscription = $tenant->subscription) {
            $plan = $subscription->plan;
            
            $request->merge([
                'subscription' => $subscription,
                'plan' => $plan,
                'max_clinics' => $subscription->max_clinics ?? ($plan ? $plan->max_clinics : 10),
                'max_users' => $subscription->max_users ?? ($plan ? $plan->max_users : 5),
                'max_screens' => $subscription->max_screens ?? ($plan ? ($plan->max_screens ?? 1) : 1),
            ]);
        }

        return $next($request);
    }
}

