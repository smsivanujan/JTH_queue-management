<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Show subscription required page
     */
    public function required()
    {
        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app('tenant');

        $subscription = $tenant->subscription;
        $isOnTrial = $tenant->isOnTrial();

        return view('subscription.required', compact('tenant', 'subscription', 'isOnTrial'));
    }

    /**
     * Show subscription management page (billing page)
     */
    public function index()
    {
        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app('tenant');

        $subscription = $tenant->subscription;
        $subscriptions = $tenant->subscriptions()->latest()->get();
        $currentPlan = $subscription?->plan;
        $availablePlans = \App\Models\Plan::active()->ordered()->get();
        $isExpired = $subscription && ($subscription->isExpired() || ($subscription->ends_at && $subscription->ends_at->isPast()));
        $isOnTrial = $tenant->isOnTrial();

        // Calculate usage statistics
        $clinicsUsed = $tenant->clinics()->count();
        $staffUsed = $tenant->users()->wherePivot('is_active', true)->count();
        $activeScreensCount = \App\Models\ActiveScreen::getActiveCount($tenant->id, null, 30);
        
        // Get limits from subscription or plan
        $maxClinics = null;
        $maxUsers = null;
        $maxScreens = null;
        
        if ($subscription && $subscription->isActive()) {
            $maxClinics = $subscription->max_clinics ?? ($currentPlan ? $currentPlan->max_clinics : null);
            $maxUsers = $subscription->max_users ?? ($currentPlan ? $currentPlan->max_users : null);
            $maxScreens = $subscription->max_screens ?? ($currentPlan ? $currentPlan->max_screens : null);
        } elseif ($currentPlan) {
            $maxClinics = $currentPlan->max_clinics;
            $maxUsers = $currentPlan->max_users;
            $maxScreens = $currentPlan->max_screens;
        }

        // Calculate trial days remaining
        $trialDaysRemaining = null;
        if ($isOnTrial && $tenant->trial_ends_at) {
            $trialDaysRemaining = max(0, now()->diffInDays($tenant->trial_ends_at, false));
        }

        // Check if Stripe is configured
        $stripeEnabled = !empty(config('services.stripe.key')) && !empty(config('services.stripe.secret'));

        // Determine recommended plan (Pro plan is recommended)
        $recommendedPlanSlug = 'pro';

        return view('subscription.index', compact(
            'tenant', 
            'subscription', 
            'subscriptions', 
            'currentPlan', 
            'availablePlans', 
            'isExpired',
            'isOnTrial',
            'clinicsUsed',
            'staffUsed',
            'activeScreensCount',
            'maxClinics',
            'maxUsers',
            'maxScreens',
            'trialDaysRemaining',
            'stripeEnabled',
            'recommendedPlanSlug'
        ));
    }
}

