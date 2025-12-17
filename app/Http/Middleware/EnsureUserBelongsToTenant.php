<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToTenant
{
    /**
     * Handle an incoming request.
     *
     * Ensures authenticated user belongs to the current tenant
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        $tenant = $request->get('tenant') ?? (app()->bound('tenant') ? app('tenant') : null);

        if (!$tenant) {
            return $next($request);
        }

        // Super Admin can enter any tenant context (no membership check)
        if ($user->isSuperAdmin()) {
            // Ensure user's current tenant is set to match the identified tenant
            if ($user->current_tenant_id !== $tenant->id) {
                $user->update(['current_tenant_id' => $tenant->id]);
            }
            return $next($request);
        }

        // Regular users: Check if user belongs to tenant
        if (!$user->belongsToTenant($tenant->id)) {
            abort(403, 'You do not have access to this organization.');
        }

        // Ensure user's current tenant is set
        if ($user->current_tenant_id !== $tenant->id) {
            $user->switchTenant($tenant);
        }

        return $next($request);
    }
}

