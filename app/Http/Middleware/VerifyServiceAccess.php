<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verify Service Access Middleware
 * 
 * Checks if user has verified access to a service via password.
 * Similar to VerifyOPDLabAccess but works with any service.
 */
class VerifyServiceAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get service from route parameter (route model binding)
        $service = $request->route('service');
        
        if (!$service) {
            // Try to get service ID from route parameter
            $serviceId = $request->route('service');
            if (is_numeric($serviceId)) {
                $service = \App\Models\Service::find($serviceId);
            }
        }
        
        if (!$service) {
            abort(404, 'Service not found');
        }
        
        // Check if service is verified in session
        $serviceVerified = session("service_{$service->id}_verified", false);
        
        // Super Admin bypass: Platform admins can access any service without password
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            $tenant = app()->bound('tenant') ? app('tenant') : null;
            if ($tenant && $service->tenant_id === $tenant->id) {
                return $next($request);
            }
        }
        
        // For second screen route, allow access if user is authenticated and belongs to tenant
        // (similar to OPD Lab middleware behavior)
        if (!$serviceVerified && $request->routeIs('service.second-screen')) {
            if (auth()->check()) {
                $tenant = app()->bound('tenant') ? app('tenant') : null;
                if ($tenant && auth()->user()->belongsToTenant($tenant->id) && $service->tenant_id === $tenant->id) {
                    return $next($request);
                }
            }
        }
        
        if (!$serviceVerified) {
            // Redirect to dashboard or show error
            return redirect()->route('app.dashboard')
                ->withErrors(['message' => "Access to {$service->name} requires password verification."]);
        }
        
        return $next($request);
    }
}
