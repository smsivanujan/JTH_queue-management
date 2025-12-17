<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * Identifies tenant ONLY from authenticated user's current_tenant_id.
     * This is the single source of truth for tenant identification.
     * 
     * REQUIREMENTS:
     * - User must be authenticated (runs after 'auth' middleware)
     * - User must have current_tenant_id set
     * - Super Admin must have explicitly entered a tenant context
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure user is authenticated (this middleware should run AFTER 'auth' middleware)
        if (!auth()->check()) {
            // If not authenticated, allow middleware chain to continue
            // This allows public routes to work
            return $next($request);
        }

        $user = auth()->user();
        $tenant = null;

        // SOURCE OF TRUTH: current_tenant_id from authenticated user
        if ($user->current_tenant_id) {
            try {
                $tenant = Tenant::find($user->current_tenant_id);
                
                // Verify tenant exists and is active
                if ($tenant && !$tenant->is_active) {
                    // Tenant is inactive, don't use it
                    $tenant = null;
                }
            } catch (\Exception $e) {
                // Tenant not found or database error
                $tenant = null;
            }
        }

        // Set tenant in request and service container (if found)
        if ($tenant) {
            $request->merge(['tenant' => $tenant]);
            app()->instance('tenant', $tenant);
            app()->instance('tenant_id', $tenant->id);
        }

        // Continue middleware chain regardless of whether tenant was found
        // Route-level middleware will handle tenant requirements
        return $next($request);
    }
}
