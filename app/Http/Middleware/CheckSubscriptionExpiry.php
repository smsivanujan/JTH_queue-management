<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckSubscriptionExpiry
{
    /**
     * Handle an incoming request.
     *
     * Automatically expires subscriptions that have passed their end date
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $request->get('tenant') ?? (app()->bound('tenant') ? app('tenant') : null);

        if (!$tenant) {
            return $next($request);
        }

        // Check all subscriptions for expiry
        $subscriptions = $tenant->subscriptions()
            ->where('status', '!=', \App\Models\Subscription::STATUS_EXPIRED)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->get();

        foreach ($subscriptions as $subscription) {
            $subscription->expire();
            Log::info("Subscription expired automatically", [
                'subscription_id' => $subscription->id,
                'tenant_id' => $tenant->id,
                'expired_at' => $subscription->ends_at,
            ]);
        }

        return $next($request);
    }
}

