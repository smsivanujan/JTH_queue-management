<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * Ensures the authenticated user has one of the required roles in the current tenant
     * 
     * Usage: middleware('role:admin,reception') or middleware('role:admin')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->withErrors(['Please log in to access this resource.']);
        }

        // Tenant is guaranteed to be set by IdentifyTenant middleware
        // This middleware runs AFTER tenant middleware, so tenant must exist
        $tenant = app()->bound('tenant') ? app('tenant') : null;

        if (!$tenant) {
            \Log::error('EnsureUserHasRole: Tenant not set', [
                'user_id' => auth()->id(),
                'current_tenant_id' => auth()->user()?->current_tenant_id,
            ]);
            abort(500, 'System error: Tenant context not available');
        }

        $user = auth()->user();

        // Super Admin bypass: Platform admins have all roles
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user belongs to tenant
        if (!$user->belongsToTenant($tenant->id)) {
            abort(403, 'You do not have access to this organization.');
        }

        // Check if user has one of the required roles
        if (!$user->hasRoleInTenant($tenant->id, $roles)) {
            $rolesList = implode(', ', $roles);
            abort(403, "Access denied. Required role(s): {$rolesList}");
        }

        return $next($request);
    }
}

