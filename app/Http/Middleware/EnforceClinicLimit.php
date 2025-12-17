<?php

namespace App\Http\Middleware;

use App\Helpers\SubscriptionHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceClinicLimit
{
    /**
     * Handle an incoming request.
     *
     * Enforces clinic creation limit based on subscription plan
     * Super Admins bypass all limit checks
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Super Admin bypass: Platform admins can create unlimited clinics
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return $next($request);
        }

        $tenant = $request->get('tenant') ?? (app()->bound('tenant') ? app('tenant') : null);

        if (!$tenant) {
            return $next($request);
        }

        // Check if tenant can create more clinics
        if (!SubscriptionHelper::canCreateClinic()) {
            $plan = SubscriptionHelper::getCurrentPlan();
            $maxClinics = $plan && $plan->hasUnlimitedClinics() 
                ? 'unlimited' 
                : ($plan ? $plan->max_clinics : 'N/A');
            
            return response()->json([
                'success' => false,
                'message' => "Clinic limit reached. Your plan allows {$maxClinics} clinics. Please upgrade to add more clinics.",
            ], 403);
        }

        return $next($request);
    }
}

