<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Str;

class TenantService
{
    /**
     * Create a new tenant with initial subscription
     */
    public function createTenant(array $data, User $owner, string $planName = 'trial'): Tenant
    {
        // Generate slug from name
        $slug = $this->generateUniqueSlug($data['name']);

        // Create tenant
        $tenant = Tenant::create([
            'name' => $data['name'],
            'slug' => $slug,
            'email' => $data['email'] ?? $owner->email,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'is_active' => true,
            'trial_ends_at' => now()->addDays(14), // 14-day trial
        ]);

        // Create initial subscription
        $this->createSubscription($tenant, $planName);

        // Attach owner to tenant
        $tenant->users()->attach($owner->id, [
            'role' => 'owner',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        // Set as user's current tenant
        $owner->update(['current_tenant_id' => $tenant->id]);

        return $tenant;
    }

    /**
     * Create subscription for tenant
     */
    public function createSubscription(Tenant $tenant, string $planSlug, ?\DateTime $endsAt = null): Subscription
    {
        // Find plan by slug
        $plan = \App\Models\Plan::findBySlug($planSlug);
        
        if (!$plan) {
            throw new \Exception("Plan '{$planSlug}' not found");
        }

        // Calculate end date if not provided
        if (!$endsAt && $plan->billing_cycle === 'monthly') {
            $endsAt = now()->addMonth();
        } elseif (!$endsAt && $plan->billing_cycle === 'yearly') {
            $endsAt = now()->addYear();
        }

        return Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'plan_name' => $plan->slug, // Keep for backward compatibility
            'status' => $planSlug === 'trial' ? Subscription::STATUS_TRIAL : Subscription::STATUS_ACTIVE,
            'max_clinics' => $plan->max_clinics,
            'max_users' => $plan->max_users,
            'max_screens' => $plan->max_screens ?? 1,
            'starts_at' => now(),
            'ends_at' => $endsAt,
            'features' => $plan->features ?? [],
        ]);
    }

    /**
     * Generate unique slug for tenant
     */
    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if tenant can create more clinics
     */
    public function canCreateClinic(Tenant $tenant): bool
    {
        $subscription = $tenant->subscription;

        if (!$subscription) {
            return false;
        }

        // Unlimited
        if ($subscription->max_clinics === -1) {
            return true;
        }

        $currentClinicCount = $tenant->clinics()->count();

        return $currentClinicCount < $subscription->max_clinics;
    }

    /**
     * Check if tenant can add more users
     */
    public function canAddUser(Tenant $tenant): bool
    {
        $subscription = $tenant->subscription;

        if (!$subscription) {
            return false;
        }

        // Unlimited
        if ($subscription->max_users === -1) {
            return true;
        }

        $currentUserCount = $tenant->users()->wherePivot('is_active', true)->count();

        return $currentUserCount < $subscription->max_users;
    }
}

