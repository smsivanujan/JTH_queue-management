<?php

namespace App\Http\Middleware;

use App\Models\Clinic;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeQueueAccess
{
    /**
     * Handle an incoming request.
     *
     * Ensures user has access to the queue/clinic (password verified or authorized)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Support both route model binding (clinic) and legacy (clinicId)
        $clinic = $request->route('clinic');
        $clinicId = $clinic ? $clinic->id : ($request->route('clinicId') ?? $request->input('clinic_id'));
        
        if (!$clinicId) {
            abort(400, 'Clinic ID is required');
        }

        // Tenant is guaranteed to be set by IdentifyTenant middleware
        // This middleware runs AFTER tenant middleware, so tenant must exist
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            \Log::error('AuthorizeQueueAccess: Tenant not set', [
                'clinic_id' => $clinicId,
                'user_id' => auth()->id(),
                'current_tenant_id' => auth()->user()?->current_tenant_id,
            ]);
            abort(500, 'System error: Tenant context not available');
        }

        // If clinic model not provided, fetch it (already scoped by route model binding if used)
        if (!$clinic) {
            $clinic = Clinic::find($clinicId);
            
            if (!$clinic || $clinic->tenant_id !== $tenant->id) {
                abort(403, 'Unauthorized access to this clinic');
            }
        }

        // Access is now role-based only (no password required)
        // Check if user is authenticated and belongs to tenant
        if (auth()->check()) {
            $user = auth()->user();
            
            // Super Admin bypass: Platform admins can access any queue
            if ($user->isSuperAdmin()) {
                return $next($request);
            }
            
            // Allow access for authenticated users who belong to the tenant
            if ($user->belongsToTenant($tenant->id)) {
                return $next($request);
            }
        }
        
        // Unauthenticated users or users not belonging to tenant are denied
        abort(403, 'Access denied. You must be authenticated and belong to this organization.');

        return $next($request);
    }
}

