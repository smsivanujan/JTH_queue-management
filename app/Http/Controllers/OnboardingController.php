<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
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
        
        // Onboarding needed if tenant has no clinics
        return !$hasClinics;
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
        
        return view('onboarding.index', compact('hasClinics'));
    }

    /**
     * Show step 2: Create first clinic
     */
    public function clinic()
    {
        $tenant = app('tenant');
        
        // Check if clinic already exists
        if (Clinic::where('tenant_id', $tenant->id)->exists()) {
            return redirect()->route('app.onboarding.complete');
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
            return redirect()->route('app.onboarding.complete');
        }
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:4', 'max:255'],
            'queue_type' => ['required', 'in:sequential,range'],
            'display_count' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        try {
            $clinic = Clinic::withoutGlobalScopes()->create([
                'name' => $validated['name'],
                'password' => $validated['password'] ?? '1234',
                'tenant_id' => $tenant->id,
            ]);

            // Create queue with the specified type
            $displayCount = $validated['display_count'] ?? 1;
            $queue = \App\Models\Queue::withoutGlobalScopes()->create([
                'clinic_id' => $clinic->id,
                'tenant_id' => $tenant->id,
                'display' => $displayCount,
                'type' => $validated['queue_type'],
            ]);

            return redirect()->route('app.onboarding.complete')
                ->with('success', 'Location created successfully!');
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
     * Show completion screen
     */
    public function complete()
    {
        $tenant = app('tenant');
        
        // Get the created clinic for display
        $clinic = Clinic::where('tenant_id', $tenant->id)->first();
        
        return view('onboarding.complete', compact('clinic'));
    }

    /**
     * Skip onboarding
     */
    public function skip()
    {
        return redirect()->route('app.dashboard')
            ->with('info', 'You can create locations anytime from the dashboard.');
    }
}

