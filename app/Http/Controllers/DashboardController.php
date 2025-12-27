<?php

namespace App\Http\Controllers;

use App\Models\Clinic;

class DashboardController extends Controller
{
    public function index()
    {
        // Tenant is guaranteed to be set by IdentifyTenant middleware
        // EnsureTenantAccess middleware handles redirects if tenant is missing
        $tenant = app('tenant');

        // Check if onboarding is needed
        if (\App\Http\Controllers\OnboardingController::isOnboardingNeeded()) {
            return redirect()->route('app.onboarding.index');
        }

        // Get clinics for current tenant (automatically scoped by TenantScope)
        // Super Admin sees clinics for the tenant they've entered
        // Regular users see clinics for their tenant
        // Note: Show all clinics, not just ones with queues (new clinics may not have queues yet)
        $clinics = Clinic::orderBy('name')->get();
        
        return view('dashboard', compact('clinics', 'tenant'));
    }
}
