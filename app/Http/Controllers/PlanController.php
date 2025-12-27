<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display available plans for public pricing page (no auth required)
     */
    public function publicIndex()
    {
        // Get all active plans for public viewing
        $plans = Plan::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();

        return view('pricing', compact('plans'));
    }

    /**
     * Display all available plans (for tenant self-activation)
     */
    public function index()
    {
        $plans = Plan::active()->ordered()->get();
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        $currentSubscription = $tenant?->subscription;
        $currentPlan = $currentSubscription?->plan;
        
        return view('plans.index', compact('plans', 'tenant', 'currentSubscription', 'currentPlan'));
    }

    /**
     * Activate a plan for current tenant (manual activation)
     * No payment gateway - for tenant self-service
     */
    public function activate(Request $request, Plan $plan)
    {
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return back()->withErrors(['error' => 'No active organization selected.']);
        }

        // Validate plan is active
        if (!$plan->is_active) {
            return back()->withErrors(['error' => 'This plan is not available.']);
        }

        // Cancel existing active subscriptions
        $tenant->subscriptions()
            ->where('status', Subscription::STATUS_ACTIVE)
            ->update([
                'status' => Subscription::STATUS_CANCELLED,
                'cancelled_at' => now(),
            ]);

        // Calculate end date based on billing cycle
        $endsAt = null;
        if ($plan->billing_cycle === 'monthly') {
            $endsAt = now()->addMonth();
        } elseif ($plan->billing_cycle === 'yearly') {
            $endsAt = now()->addYear();
        }
        // For one_time or trial, ends_at can be null or based on trial_days
        if ($plan->trial_days > 0 && $plan->slug === 'trial') {
            $endsAt = now()->addDays($plan->trial_days);
        }

        // Create new subscription
        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'plan_name' => $plan->slug,
            'status' => $plan->slug === 'trial' ? Subscription::STATUS_TRIAL : Subscription::STATUS_ACTIVE,
            'max_clinics' => $plan->max_clinics,
            'max_users' => $plan->max_users,
            'max_screens' => $plan->max_screens ?? 1,
            'starts_at' => now(),
            'ends_at' => $endsAt,
            'features' => $plan->features ?? [],
        ]);

        // Generate invoice for manual payment
        if ($plan->slug !== 'trial') {
            $invoice = \App\Services\InvoiceService::generateInvoice($subscription, $plan, \App\Models\Invoice::PAYMENT_METHOD_MANUAL);
            
            // Mark as paid immediately for manual activation (since admin/tenant is activating it directly)
            if ($subscription->status === Subscription::STATUS_ACTIVE) {
                \App\Services\InvoiceService::markInvoiceAsPaid($subscription, \App\Models\Invoice::PAYMENT_METHOD_MANUAL);
                
                // Send payment success email for manual payments (with cooldown)
                if (!AutomationLog::wasSentRecently($subscription->tenant_id, AutomationLog::TYPE_PAYMENT_SUCCESS, null, 24)) {
                    try {
                        $primaryUser = $subscription->tenant->users()->wherePivot('role', 'admin')->first() 
                            ?? $subscription->tenant->users()->first();
                        
                        if ($primaryUser) {
                            Mail::to($primaryUser->email)->send(new PaymentSuccess($subscription->tenant, $subscription));
                            AutomationLog::logSent($subscription->tenant_id, AutomationLog::TYPE_PAYMENT_SUCCESS, null, [
                                'subscription_id' => $subscription->id,
                                'payment_method' => 'manual',
                                'recipient' => $primaryUser->email,
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to send payment success email', [
                            'subscription_id' => $subscription->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        return redirect()->route('app.subscription.index')
            ->with('success', "Successfully activated {$plan->name} plan.");
    }

    /**
     * Activate a plan for a specific tenant (admin function)
     * This is for admin use - no payment gateway
     */
    public function activateForTenant(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
            'ends_at' => ['nullable', 'date', 'after:today'],
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);

        // Cancel existing active subscriptions
        $tenant->subscriptions()
            ->where('status', Subscription::STATUS_ACTIVE)
            ->update([
                'status' => Subscription::STATUS_CANCELLED,
                'cancelled_at' => now(),
            ]);

        // Calculate end date
        $endsAt = $validated['ends_at'] 
            ? new \DateTime($validated['ends_at'])
            : ($plan->billing_cycle === 'monthly' ? now()->addMonth() : now()->addYear());

        // Create new subscription
        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'plan_name' => $plan->slug,
            'status' => Subscription::STATUS_ACTIVE,
            'max_clinics' => $plan->max_clinics,
            'max_users' => $plan->max_users,
            'max_screens' => $plan->max_screens ?? 1,
            'starts_at' => now(),
            'ends_at' => $endsAt,
            'features' => $plan->features,
        ]);

        // Generate invoice for manual payment
        if ($plan->slug !== 'trial' && $plan->price > 0) {
            $invoice = \App\Services\InvoiceService::generateInvoice($subscription, $plan, \App\Models\Invoice::PAYMENT_METHOD_MANUAL);
            // Mark as paid immediately for admin activation
            \App\Services\InvoiceService::markInvoiceAsPaid($subscription, \App\Models\Invoice::PAYMENT_METHOD_MANUAL);
            
            // Send payment success email for manual payments (with cooldown)
            if (!AutomationLog::wasSentRecently($subscription->tenant_id, AutomationLog::TYPE_PAYMENT_SUCCESS, null, 24)) {
                try {
                    $primaryUser = $subscription->tenant->users()->wherePivot('role', 'admin')->first() 
                        ?? $subscription->tenant->users()->first();
                    
                    if ($primaryUser) {
                        Mail::to($primaryUser->email)->send(new PaymentSuccess($subscription->tenant, $subscription));
                        AutomationLog::logSent($subscription->tenant_id, AutomationLog::TYPE_PAYMENT_SUCCESS, null, [
                            'subscription_id' => $subscription->id,
                            'payment_method' => 'manual',
                            'recipient' => $primaryUser->email,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send payment success email', [
                        'subscription_id' => $subscription->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return redirect()->back()
            ->with('success', "Plan '{$plan->name}' activated successfully for {$tenant->name}");
    }

    /**
     * Renew current tenant's subscription
     */
    public function renew(Request $request, Plan $plan)
    {
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant || !$tenant->subscription || $tenant->subscription->plan_id !== $plan->id) {
            return back()->withErrors(['error' => 'No active subscription for this plan to renew.']);
        }

        $subscription = $tenant->subscription;
        
        // Calculate new end date based on billing cycle
        $newEndDate = null;
        if ($plan->billing_cycle === 'monthly') {
            $newEndDate = ($subscription->ends_at && $subscription->ends_at->isFuture()) 
                ? $subscription->ends_at->addMonth() 
                : now()->addMonth();
        } elseif ($plan->billing_cycle === 'yearly') {
            $newEndDate = ($subscription->ends_at && $subscription->ends_at->isFuture()) 
                ? $subscription->ends_at->addYear() 
                : now()->addYear();
        } else {
            $newEndDate = now()->addYear(); // Default to 1 year
        }

        $subscription->renew($newEndDate);

        return redirect()->route('subscription.index')
            ->with('success', "Successfully renewed {$plan->name} plan.");
    }
}

