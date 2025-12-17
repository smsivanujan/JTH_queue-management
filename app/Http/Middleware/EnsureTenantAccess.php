<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAccess
{
    /**
     * Handle an incoming request.
     *
     * Ensures the user has access to the current tenant.
     * This middleware should be used after tenant identification.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $request->get('tenant') ?? (app()->bound('tenant') ? app('tenant') : null);

        if (!$tenant) {
            // If no tenant is identified, check if user is authenticated
            if (auth()->check()) {
                $user = auth()->user();
                
                // Super Admin can access platform-level routes without tenant
                if ($user->isSuperAdmin()) {
                    // Allow access to tenant selection and platform routes
                    if ($request->routeIs('tenant.select', 'tenant.switch', 'tenant.register', 'tenant.enter', 'tenant.exit', 'platform.dashboard')) {
                        return $next($request);
                    }
                    // Redirect Super Admin to platform dashboard if they need tenant context
                    return redirect()->route('platform.dashboard')
                        ->with('info', 'Please select an organization to manage.');
                }
                
                // Regular users: Allow access to tenant selection routes
                if ($request->routeIs('tenant.select', 'tenant.switch', 'tenant.register')) {
                    return $next($request);
                }
                
                // Redirect to tenant selection if tenant is required
                return redirect()->route('tenant.select')
                    ->withErrors(['Please select an organization to continue.']);
            }
            
            // For unauthenticated users, allow access to public routes
            if ($this->isPublicRoute($request)) {
                return $next($request);
            }
            
            // Redirect to login for protected routes
            return redirect()->route('login')
                ->withErrors(['Please log in to access this resource.']);
        }

        // Verify tenant is active
        if (!$tenant->is_active) {
            abort(403, 'This organization account has been deactivated.');
        }

        // If user is authenticated, verify they belong to this tenant
        if (auth()->check()) {
            $user = auth()->user();
            
            // Super Admin can access any tenant they've entered via current_tenant_id
            if ($user->isSuperAdmin()) {
                // Verify Super Admin has explicitly entered this tenant
                if ($user->current_tenant_id !== $tenant->id) {
                    // Super Admin trying to access different tenant than they entered
                    return redirect()->route('platform.dashboard')
                        ->withErrors(['Please select an organization to manage.']);
                }
                // Super Admin has entered this tenant, allow access
                return $next($request);
            }
            
            // Regular users: Check if user belongs to this tenant
            if (!$user->tenants()->where('tenants.id', $tenant->id)->exists()) {
                // User doesn't belong to this tenant
                // Clear tenant from session and redirect
                session()->forget('current_tenant_id');
                $user->current_tenant_id = null;
                $user->save();
                
                return redirect()->route('tenant.select')
                    ->withErrors(['You do not have access to this organization.']);
            }
        }

        return $next($request);
    }

    /**
     * Check if route is public (doesn't require tenant)
     */
    private function isPublicRoute(Request $request): bool
    {
        // Allow login and logout routes
        if ($request->routeIs('login', 'logout')) {
            return true;
        }

        $publicRoutes = [
            'home',
            'landing',
            'tenant.register',
            'pricing',
        ];

        return $request->routeIs($publicRoutes);
    }
}

