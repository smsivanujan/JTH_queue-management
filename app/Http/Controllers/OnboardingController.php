<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Service;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    /**
     * Check if onboarding is needed for the current tenant
     */
    public static function isOnboardingNeeded(): bool
    {
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return false;
        }
        
        $hasClinics = Clinic::where('tenant_id', $tenant->id)->exists();
        $hasServices = Service::where('tenant_id', $tenant->id)->exists();
        
        // Onboarding needed if tenant has no clinics OR no services
        return !$hasClinics || !$hasServices;
    }

    /**
     * Show onboarding welcome screen
     */
    public function index()
    {
        $tenant = app('tenant');
        
        // Check if onboarding is still needed
        if (!self::isOnboardingNeeded()) {
            return redirect()->route('app.dashboard');
        }
        
        // Determine which steps are needed
        $hasClinics = Clinic::where('tenant_id', $tenant->id)->exists();
        $hasServices = Service::where('tenant_id', $tenant->id)->exists();
        
        return view('onboarding.index', compact('hasClinics', 'hasServices'));
    }

    /**
     * Show step 2: Create first clinic
     */
    public function clinic()
    {
        $tenant = app('tenant');
        
        // Check if clinic already exists
        if (Clinic::where('tenant_id', $tenant->id)->exists()) {
            return redirect()->route('app.onboarding.service');
        }
        
        return view('onboarding.clinic');
    }

    /**
     * Store first clinic from onboarding
     */
    public function storeClinic(Request $request)
    {
        $tenant = app('tenant');
        
        // Check if clinic already exists
        if (Clinic::where('tenant_id', $tenant->id)->exists()) {
            return redirect()->route('app.onboarding.service');
        }
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:4', 'max:255'],
        ]);

        try {
            Clinic::withoutGlobalScopes()->create([
                'name' => $validated['name'],
                'password' => $validated['password'] ?? '1234',
                'tenant_id' => $tenant->id,
            ]);

            return redirect()->route('app.onboarding.service')
                ->with('success', 'Clinic created successfully!');
        } catch (\Exception $e) {
            \Log::error('Onboarding clinic creation failed', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant->id ?? null,
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create clinic. Please try again.']);
        }
    }

    /**
     * Show step 3: Create first service
     */
    public function service()
    {
        $tenant = app('tenant');
        
        // Check if service already exists
        if (Service::where('tenant_id', $tenant->id)->exists()) {
            return redirect()->route('app.onboarding.complete');
        }
        
        return view('onboarding.service');
    }

    /**
     * Store first service from onboarding
     */
    public function storeService(Request $request)
    {
        $tenant = app('tenant');
        
        // Check if service already exists
        if (Service::where('tenant_id', $tenant->id)->exists()) {
            return redirect()->route('app.onboarding.complete');
        }
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:sequential,range'],
            'password' => ['nullable', 'string', 'min:4', 'max:255'],
        ]);

        try {
            $service = Service::create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'tenant_id' => $tenant->id,
                'is_active' => true,
            ]);

            // Set password if provided
            if (!empty($validated['password'])) {
                $service->setPassword($validated['password']);
            }

            return redirect()->route('app.onboarding.complete');
        } catch (\Exception $e) {
            \Log::error('Onboarding service creation failed', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant->id ?? null,
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create service. Please try again.']);
        }
    }

    /**
     * Show completion screen
     */
    public function complete()
    {
        $tenant = app('tenant');
        
        // Get the created clinic and service for display
        $clinic = Clinic::where('tenant_id', $tenant->id)->first();
        $service = Service::where('tenant_id', $tenant->id)->first();
        
        return view('onboarding.complete', compact('clinic', 'service'));
    }

    /**
     * Skip onboarding
     */
    public function skip()
    {
        return redirect()->route('app.dashboard')
            ->with('info', 'You can create clinics and services anytime from the dashboard.');
    }
}

