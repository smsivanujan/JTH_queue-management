<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * Ensures the authenticated user is a Super Admin
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->withErrors(['Please log in to access this resource.']);
        }

        $user = auth()->user();

        if (!$user->isSuperAdmin()) {
            abort(403, 'Only platform administrators can access this area.');
        }

        return $next($request);
    }
}

