<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ActiveScreen;

class EnforceScreenLimit
{
    /**
     * Handle an incoming request.
     *
     * Enforces screen limit based on subscription plan
     * Uses database-based tracking with session fallback for backward compatibility
     * Super Admins bypass all limit checks
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Super Admin bypass: Platform admins can use unlimited screens
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return $next($request);
        }

        $tenant = $request->get('tenant') ?? (app()->bound('tenant') ? app('tenant') : null);

        if (!$tenant) {
            return $next($request);
        }

        $subscription = $tenant->subscription;

        if (!$subscription || !$subscription->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Active subscription required to use display screens.',
            ], 403);
        }

        $plan = $subscription->plan;
        $maxScreens = $subscription->max_screens ?? ($plan ? $plan->max_screens : 1);

        // Check if unlimited (-1 means unlimited)
        if ($maxScreens === -1) {
            return $next($request);
        }

        // Primary: Use database-based tracking (new, reliable)
        $activeScreens = ActiveScreen::getActiveCount($tenant->id);
        
        // Fallback: Use session-based tracking (backward compatibility)
        // Only use if database count is 0 (might be during migration period)
        if ($activeScreens === 0) {
            $sessionActiveScreens = session('active_screens', 0);
            $activeScreens = $sessionActiveScreens;
        }
        
        if ($activeScreens >= $maxScreens) {
            return response()->json([
                'success' => false,
                'message' => "Screen limit reached. Your plan allows {$maxScreens} display screen(s). Please upgrade to use more screens.",
            ], 403);
        }

        // Note: We no longer increment session here
        // Screen registration happens in ScreenController when screen is actually opened
        // This middleware only checks the limit before allowing registration

        return $next($request);
    }
}
