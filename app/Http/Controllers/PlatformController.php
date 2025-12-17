<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlatformController extends Controller
{
    /**
     * Show platform dashboard for Super Admin
     * Lists all tenants with their subscription status and plans
     */
    public function dashboard()
    {
        // Only Super Admin can access platform dashboard
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Only platform administrators can access this page.');
        }

        // Get all tenants - subscription will be lazy loaded via relationship accessor
        $tenants = Tenant::orderBy('name')->get();
        
        // Load subscription plans for tenants that have subscriptions
        $tenants->load(['subscriptions.plan']);

        // Get statistics
        $stats = [
            'total_tenants' => $tenants->count(),
            'active_tenants' => $tenants->where('is_active', true)->count(),
            'inactive_tenants' => $tenants->where('is_active', false)->count(),
            'tenants_with_subscription' => $tenants->filter(function ($tenant) {
                return $tenant->subscription && $tenant->subscription->isActive();
            })->count(),
            'tenants_on_trial' => $tenants->filter(function ($tenant) {
                return $tenant->isOnTrial();
            })->count(),
        ];

        return view('platform.dashboard', compact('tenants', 'stats'));
    }
}

