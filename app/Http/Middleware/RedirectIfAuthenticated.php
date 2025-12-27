<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * Redirect authenticated users away from login/register pages
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // If user has a current tenant, redirect to app dashboard
                if ($user->current_tenant_id) {
                    return redirect()->route('app.dashboard');
                }

                // Super Admin goes to platform dashboard
                if ($user->isSuperAdmin()) {
                    return redirect()->route('platform.dashboard');
                }

                // Regular users go to tenant selection
                return redirect()->route('tenant.select');
            }
        }

        return $next($request);
    }
}
