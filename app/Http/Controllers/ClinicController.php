<?php

namespace App\Http\Controllers;

use App\Helpers\SubscriptionHelper;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClinicController extends Controller
{
    /**
     * Display a listing of clinics for the current tenant
     */
    public function index()
    {
        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app('tenant');

        // Get all clinics for this tenant (automatically scoped by TenantScope)
        $clinics = Clinic::orderBy('name')->get();
        
        // Get clinic limit info for display
        $canCreateMore = SubscriptionHelper::canCreateClinic();
        $plan = SubscriptionHelper::getCurrentPlan();
        $currentCount = $clinics->count();
        
        $maxClinics = null;
        if ($plan) {
            $subscription = $tenant->subscription;
            $maxClinics = $subscription ? ($subscription->max_clinics ?? $plan->max_clinics) : $plan->max_clinics;
        }

        return view('clinic.index', compact('clinics', 'tenant', 'canCreateMore', 'maxClinics', 'currentCount'));
    }

    /**
     * Show the form for creating a new clinic
     */
    public function create()
    {
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return redirect()->route('tenant.select')
                ->withErrors(['Please select an organization.']);
        }

        // Check clinic limit
        if (!SubscriptionHelper::canCreateClinic()) {
            $plan = SubscriptionHelper::getCurrentPlan();
            $currentCount = $tenant->clinics()->count();
            $subscription = $tenant->subscription;
            $maxClinics = $subscription ? ($subscription->max_clinics ?? ($plan ? $plan->max_clinics : 'N/A')) : ($plan ? $plan->max_clinics : 'N/A');
            
            return redirect()->route('app.clinic.index')
                ->withErrors(['error' => "Clinic limit reached. Your plan allows {$maxClinics} clinic(s). Please upgrade to add more clinics."]);
        }

        return view('clinic.create');
    }

    /**
     * Store a newly created clinic
     */
    public function store(Request $request)
    {
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return redirect()->route('tenant.select')
                ->withErrors(['Please select an organization.']);
        }

        // Check clinic limit before validation
        if (!SubscriptionHelper::canCreateClinic()) {
            $plan = SubscriptionHelper::getCurrentPlan();
            $currentCount = $tenant->clinics()->count();
            $subscription = $tenant->subscription;
            $maxClinics = $subscription ? ($subscription->max_clinics ?? ($plan ? $plan->max_clinics : 'N/A')) : ($plan ? $plan->max_clinics : 'N/A');
            
            return back()
                ->withInput()
                ->withErrors(['error' => "Clinic limit reached. Your plan allows {$maxClinics} clinic(s). Please upgrade to add more clinics."]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:4', 'max:255'],
        ]);

        try {
            // Create clinic (tenant_id will be set automatically by TenantScope or we set it explicitly)
            // Use withoutGlobalScopes temporarily to bypass TenantScope during creation
            // since we're explicitly setting tenant_id
            $clinic = Clinic::withoutGlobalScopes()->create([
                'name' => $validated['name'],
                'password' => $validated['password'] ?? '1234', // Default password if not provided
                'tenant_id' => $tenant->id,
            ]);

            return redirect()->route('app.clinic.index')
                ->with('success', 'Clinic created successfully.');
        } catch (\Exception $e) {
            // Log the actual error for debugging
            \Log::error('Clinic creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'tenant_id' => $tenant->id ?? null,
                'input' => $validated ?? null,
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create clinic. Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing a clinic
     */
    public function edit(Clinic $clinic)
    {
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return redirect()->route('tenant.select')
                ->withErrors(['Please select an organization.']);
        }

        // Clinic is already validated via route model binding and TenantScope
        return view('clinic.edit', compact('clinic'));
    }

    /**
     * Update the specified clinic
     */
    public function update(Request $request, Clinic $clinic)
    {
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return redirect()->route('tenant.select')
                ->withErrors(['Please select an organization.']);
        }

        // Clinic is already validated via route model binding and TenantScope
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:4', 'max:255'],
        ]);

        try {
            $clinic->update([
                'name' => $validated['name'],
            ]);

            // Only update password if provided
            if (!empty($validated['password'])) {
                $clinic->password = $validated['password'];
                $clinic->save();
            }

            return redirect()->route('app.clinic.index')
                ->with('success', 'Clinic updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update clinic. Please try again.']);
        }
    }

    /**
     * Remove the specified clinic (soft delete or hard delete)
     */
    public function destroy(Clinic $clinic)
    {
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return redirect()->route('tenant.select')
                ->withErrors(['Please select an organization.']);
        }

        // Clinic is already validated via route model binding and TenantScope
        try {
            $clinic->delete();

            return redirect()->route('app.clinic.index')
                ->with('success', 'Clinic deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to delete clinic. Please try again.']);
        }
    }
}
