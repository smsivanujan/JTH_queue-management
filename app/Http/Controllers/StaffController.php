<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Helpers\RoleHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    /**
     * Display a listing of staff for the current tenant
     */
    public function index()
    {
        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app('tenant');

        // Get all users for this tenant
        $staff = $tenant->users()
            ->withPivot('role', 'is_active', 'joined_at')
            ->orderBy('name')
            ->get();

        // Get user limit info for display
        $canAddMore = \App\Helpers\SubscriptionHelper::canAddUser();
        $plan = \App\Helpers\SubscriptionHelper::getCurrentPlan();
        $currentCount = $tenant->users()->wherePivot('is_active', true)->count();
        
        $maxUsers = null;
        if ($plan) {
            $subscription = $tenant->subscription;
            $maxUsers = $subscription ? ($subscription->max_users ?? $plan->max_users) : $plan->max_users;
        }

        return view('staff.index', compact('staff', 'tenant', 'canAddMore', 'maxUsers', 'currentCount'));
    }

    /**
     * Show the form for creating a new staff member
     */
    public function create()
    {
        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app('tenant');

        // Check user limit
        if (!\App\Helpers\SubscriptionHelper::canAddUser()) {
            $plan = \App\Helpers\SubscriptionHelper::getCurrentPlan();
            $currentCount = $tenant->users()->wherePivot('is_active', true)->count();
            $subscription = $tenant->subscription;
            $maxUsers = $subscription ? ($subscription->max_users ?? ($plan ? $plan->max_users : 'N/A')) : ($plan ? $plan->max_users : 'N/A');
            
            return redirect()->route('app.staff.index')
                ->withErrors(['error' => "Staff limit reached. Your plan allows {$maxUsers} staff member(s). Please upgrade to add more staff."]);
        }

        $roles = RoleHelper::allRoles();
        $roleLabels = [];
        foreach ($roles as $role) {
            try {
                $roleLabels[$role] = Role::from($role)->label();
            } catch (\ValueError $e) {
                $roleLabels[$role] = ucfirst($role);
            }
        }

        return view('staff.create', compact('roles', 'roleLabels'));
    }

    /**
     * Store a newly created staff member
     */
    public function store(Request $request)
    {
        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app('tenant');

        // Check user limit before validation
        if (!\App\Helpers\SubscriptionHelper::canAddUser()) {
            $plan = \App\Helpers\SubscriptionHelper::getCurrentPlan();
            $currentCount = $tenant->users()->wherePivot('is_active', true)->count();
            $subscription = $tenant->subscription;
            $maxUsers = $subscription ? ($subscription->max_users ?? ($plan ? $plan->max_users : 'N/A')) : ($plan ? $plan->max_users : 'N/A');
            
            return back()
                ->withInput()
                ->withErrors(['error' => "Staff limit reached. Your plan allows {$maxUsers} staff member(s). Please upgrade to add more staff."]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', 'in:' . implode(',', RoleHelper::allRoles())],
        ]);

        // Use transaction to ensure data consistency
        DB::beginTransaction();
        try {
            // Check if user already exists in the system
            $user = User::where('email', $validated['email'])->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                ]);
            } else {
                // User exists, check if already linked to this tenant
                if ($user->belongsToTenant($tenant->id)) {
                    DB::rollBack();
                    return back()
                        ->withInput()
                        ->withErrors(['email' => 'This user is already a member of this organization.']);
                }
            }

            // Link user to tenant
            $tenant->users()->attach($user->id, [
                'role' => $validated['role'],
                'is_active' => true,
                'joined_at' => now(),
            ]);

            // If user doesn't have a current tenant, set this one
            if (!$user->current_tenant_id) {
                $user->update(['current_tenant_id' => $tenant->id]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to add staff member. Please try again.']);
        }

        return redirect()->route('app.staff.index')
            ->with('success', 'Staff member added successfully.');
    }

    /**
     * Show the form for editing a staff member
     */
    public function edit(User $staff)
    {
        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app('tenant');

        // User is already validated via route model binding
        $user = $staff;

        $roles = RoleHelper::allRoles();
        $roleLabels = [];
        foreach ($roles as $role) {
            try {
                $roleLabels[$role] = Role::from($role)->label();
            } catch (\ValueError $e) {
                $roleLabels[$role] = ucfirst($role);
            }
        }

        $userRole = $user->getRoleInTenant($tenant->id);
        $isActive = $user->tenants()
            ->where('tenants.id', $tenant->id)
            ->first()
            ->pivot->is_active ?? true;

        return view('staff.edit', compact('staff', 'user', 'roles', 'roleLabels', 'userRole', 'isActive', 'tenant'));
    }

    /**
     * Update the specified staff member
     */
    public function update(Request $request, User $staff)
    {
        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app('tenant');

        // User is already validated via route model binding
        $user = $staff;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'string', 'in:' . implode(',', RoleHelper::allRoles())],
            'is_active' => ['boolean'],
        ]);

        // Update user details
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update tenant relationship (role and is_active)
        $tenant->users()->updateExistingPivot($user->id, [
            'role' => $validated['role'],
            'is_active' => $request->has('is_active') ? (bool)$request->input('is_active') : true,
        ]);

        return redirect()->route('app.staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    /**
     * Remove the specified staff member from the tenant
     */
    public function destroy(User $staff)
    {
        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app('tenant');

        // User is already validated via route model binding
        $user = $staff;

        // Prevent removing the last admin
        $adminCount = $tenant->users()
            ->wherePivot('role', 'admin')
            ->wherePivot('is_active', true)
            ->count();

        if ($user->getRoleInTenant($tenant->id) === 'admin' && $adminCount <= 1) {
            return back()
                ->withErrors(['error' => 'Cannot remove the last admin from the organization.']);
        }

        // Detach user from tenant (soft delete by setting is_active to false, or detach completely)
        // We'll set is_active to false to maintain history
        $tenant->users()->updateExistingPivot($user->id, [
            'is_active' => false,
        ]);

        // If this was the user's current tenant, clear it
        if ($user->current_tenant_id === $tenant->id) {
            $user->update(['current_tenant_id' => null]);
        }

        return redirect()->route('app.staff.index')
            ->with('success', 'Staff member removed successfully.');
    }

    /**
     * Reset password for a staff member
     */
    public function resetPassword(Request $request, User $staff)
    {
        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app('tenant');

        // User is already validated via route model binding
        $user = $staff;

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('app.staff.edit', $user)
            ->with('success', 'Password reset successfully.');
    }
}

