<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class TenantController extends Controller
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Show tenant selection page
     * Super Admin sees all tenants, regular users see only their tenants
     */
    public function select()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Super Admin can see and enter any tenant
        if ($user->isSuperAdmin()) {
            $tenants = Tenant::where('is_active', true)->orderBy('name')->get();
        } else {
            // Regular users see only tenants they belong to
            $tenants = $user->tenants()->wherePivot('is_active', true)->get();
        }

        return view('tenant.select', compact('tenants'));
    }

    /**
     * Show tenant registration form
     */
    public function showRegister()
    {
        return view('tenant.register');
    }

    /**
     * Register a new tenant
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:tenants'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'user_name' => ['required', 'string', 'max:255'],
            'user_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms_accepted' => ['accepted'],
        ]);

        // Create user account
        $user = \App\Models\User::create([
            'name' => $validated['user_name'],
            'email' => $validated['user_email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Create tenant
        $tenant = $this->tenantService->createTenant([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ], $user, 'trial');

        // Login user
        Auth::login($user);

        return redirect()->route('app.dashboard')
            ->with('success', 'Organization created successfully! You are now on a 14-day trial.');
    }

    /**
     * Switch to a different tenant (enter tenant context)
     * Super Admin can enter any tenant, regular users must belong to tenant
     */
    public function switch(Request $request, Tenant $tenant)
    {
        $user = Auth::user();

        // Super Admin can enter any tenant
        if ($user->isSuperAdmin()) {
            $user->switchTenant($tenant);
            return redirect()->route('app.dashboard')
                ->with('success', "Entered tenant context: {$tenant->name}");
        }

        // Regular users must belong to the tenant
        if (!$user->belongsToTenant($tenant->id)) {
            abort(403, 'You do not have access to this organization.');
        }

        $user->switchTenant($tenant);

        return redirect()->route('app.dashboard')
            ->with('success', "Switched to {$tenant->name}");
    }

    /**
     * Exit tenant context (Super Admin only)
     * Returns Super Admin to platform level
     */
    public function exit()
    {
        $user = Auth::user();

        if (!$user->isSuperAdmin()) {
            abort(403, 'Only platform administrators can exit tenant context.');
        }

        $user->exitTenantContext();

        return redirect()->route('platform.dashboard')
            ->with('success', 'Exited tenant context. Back to platform dashboard.');
    }
}

