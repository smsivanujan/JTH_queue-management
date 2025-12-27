<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeController extends Controller
{
    public function __construct()
    {
        // Set Stripe API key
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create Stripe Checkout Session for subscription
     */
    public function checkout(Request $request, Plan $plan)
    {
        $tenant = app('tenant');
        
        if (!$tenant) {
            return back()->withErrors(['error' => 'No active organization selected.']);
        }

        // Validate plan is active and has Stripe price ID
        if (!$plan->is_active) {
            return back()->withErrors(['error' => 'This plan is not available.']);
        }

        if (!$plan->stripe_price_id) {
            return back()->withErrors(['error' => 'Stripe payment is not configured for this plan. Please use manual payment.']);
        }

        try {
            // Get or create Stripe customer
            $stripeCustomerId = $this->getOrCreateStripeCustomer($tenant);

            // Create Stripe Checkout Session
            $checkoutSession = Session::create([
                'customer' => $stripeCustomerId,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $plan->stripe_price_id,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('app.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('app.subscription.index') . '?canceled=true',
                'metadata' => [
                    'tenant_id' => $tenant->id,
                    'plan_id' => $plan->id,
                ],
                'subscription_data' => [
                    'metadata' => [
                        'tenant_id' => $tenant->id,
                        'plan_id' => $plan->id,
                    ],
                ],
            ]);

            return redirect($checkoutSession->url);
        } catch (\Exception $e) {
            Log::error('Stripe checkout failed', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
            ]);

            return back()->withErrors(['error' => 'Failed to create payment session. Please try again or use manual payment.']);
        }
    }

    /**
     * Handle successful Stripe checkout redirect
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        
        if (!$sessionId) {
            return redirect()->route('app.subscription.index')
                ->withErrors(['error' => 'Invalid payment session.']);
        }

        try {
            $session = Session::retrieve($sessionId);
            
            if ($session->payment_status === 'paid') {
                return redirect()->route('app.subscription.index')
                    ->with('success', 'Payment successful! Your subscription will be activated shortly.');
            }

            return redirect()->route('app.subscription.index')
                ->with('info', 'Payment is being processed. Your subscription will be activated once payment is confirmed.');
        } catch (\Exception $e) {
            Log::error('Stripe success page error', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
            ]);

            return redirect()->route('app.subscription.index')
                ->with('info', 'Payment is being processed. Please check your subscription status in a few moments.');
        }
    }

    /**
     * Handle Stripe webhooks
     * 
     * SECURITY NOTES:
     * - This endpoint has NO auth or tenant middleware
     * - Signature verification is the ONLY security mechanism
     * - Webhook is the ONLY source of truth for payment status
     * - Never auto-disable tenants on payment failure
     * - Super Admin can override any subscription manually
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        // Log all incoming webhook attempts (for debugging)
        Log::info('Stripe webhook received', [
            'has_signature' => !empty($sigHeader),
            'payload_size' => strlen($payload),
        ]);

        if (!$webhookSecret) {
            Log::error('Stripe webhook secret not configured');
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }

        // Verify Stripe signature - CRITICAL for security
        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
                'has_sig_header' => !empty($sigHeader),
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature verification error', [
                'error' => $e->getMessage(),
                'type' => get_class($e),
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Log verified event
        Log::info('Stripe webhook event verified', [
            'event_id' => $event->id,
            'event_type' => $event->type,
            'created' => $event->created ?? null,
        ]);

        // Handle only specific events (security: ignore unexpected events)
        try {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event->data->object, $event->id);
                    break;

                case 'invoice.payment_succeeded':
                    $this->handleInvoicePaymentSucceeded($event->data->object, $event->id);
                    break;

                case 'invoice.payment_failed':
                    $this->handleInvoicePaymentFailed($event->data->object, $event->id);
                    break;

                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event->data->object, $event->id);
                    break;

                default:
                    Log::info('Unhandled Stripe webhook event (intentionally ignored)', [
                        'event_id' => $event->id,
                        'event_type' => $event->type,
                    ]);
            }

            return response()->json(['received' => true]);
        } catch (\Exception $e) {
            Log::error('Stripe webhook handler error', [
                'error' => $e->getMessage(),
                'event_id' => $event->id ?? null,
                'event_type' => $event->type ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            // Return 200 to prevent Stripe from retrying (we'll handle manually)
            // OR return 500 if we want Stripe to retry
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Handle checkout.session.completed event
     * 
     * SECURITY: Uses metadata.tenant_id to identify tenant (never infer indirectly)
     */
    protected function handleCheckoutSessionCompleted($session, string $eventId)
    {
        Log::info('Processing checkout.session.completed', [
            'event_id' => $eventId,
            'session_id' => $session->id,
            'payment_status' => $session->payment_status ?? null,
            'subscription_id' => $session->subscription ?? null,
        ]);

        // SECURITY: Always use metadata to identify tenant (never infer)
        $tenantId = $session->metadata->tenant_id ?? null;
        $planId = $session->metadata->plan_id ?? null;

        if (!$tenantId || !$planId) {
            Log::warning('Stripe checkout session missing required metadata', [
                'event_id' => $eventId,
                'session_id' => $session->id,
                'has_tenant_id' => !empty($tenantId),
                'has_plan_id' => !empty($planId),
            ]);
            return;
        }

        $tenant = Tenant::find($tenantId);
        $plan = Plan::find($planId);

        if (!$tenant) {
            Log::error('Stripe checkout session references invalid tenant', [
                'event_id' => $eventId,
                'session_id' => $session->id,
                'tenant_id' => $tenantId,
            ]);
            return;
        }

        if (!$plan) {
            Log::error('Stripe checkout session references invalid plan', [
                'event_id' => $eventId,
                'session_id' => $session->id,
                'plan_id' => $planId,
            ]);
            return;
        }

        // Log successful checkout
        // Note: Subscription creation happens via invoice.payment_succeeded
        Log::info('Stripe checkout completed successfully', [
            'event_id' => $eventId,
            'session_id' => $session->id,
            'tenant_id' => $tenantId,
            'plan_id' => $planId,
            'payment_status' => $session->payment_status ?? null,
            'customer_id' => $session->customer ?? null,
            'subscription_id' => $session->subscription ?? null,
        ]);
    }


    /**
     * Handle customer.subscription.deleted
     * 
     * SECURITY: Marks as cancelled but does NOT immediately disable tenant
     * Super Admin can override manually if needed
     */
    protected function handleSubscriptionDeleted($stripeSubscription, string $eventId)
    {
        Log::info('Processing customer.subscription.deleted', [
            'event_id' => $eventId,
            'stripe_subscription_id' => $stripeSubscription->id,
            'customer_id' => $stripeSubscription->customer ?? null,
        ]);

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if (!$subscription) {
            Log::warning('Stripe subscription deletion event for unknown subscription', [
                'event_id' => $eventId,
                'stripe_subscription_id' => $stripeSubscription->id,
            ]);
            return;
        }

        // Mark as cancelled but DO NOT immediately expire (grace period)
        // Super Admin can manually override if needed
        $subscription->update([
            'status' => Subscription::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);

        Log::info('Stripe subscription cancelled (grace period - tenant not disabled)', [
            'event_id' => $eventId,
            'subscription_id' => $subscription->id,
            'tenant_id' => $subscription->tenant_id,
            'stripe_subscription_id' => $stripeSubscription->id,
            'note' => 'Tenant remains active - Super Admin can override if needed',
        ]);
    }

    /**
     * Handle invoice.payment_succeeded
     * 
     * SECURITY: Activates/extends subscription based on invoice subscription ID
     * This is the PRIMARY event for subscription activation/extension
     */
    protected function handleInvoicePaymentSucceeded($invoice, string $eventId)
    {
        Log::info('Processing invoice.payment_succeeded', [
            'event_id' => $eventId,
            'invoice_id' => $invoice->id,
            'subscription_id' => $invoice->subscription ?? null,
            'customer_id' => $invoice->customer ?? null,
            'amount_paid' => $invoice->amount_paid ?? null,
        ]);

        $stripeSubscriptionId = $invoice->subscription;

        if (!$stripeSubscriptionId) {
            Log::warning('Stripe invoice missing subscription ID', [
                'event_id' => $eventId,
                'invoice_id' => $invoice->id,
            ]);
            return;
        }

        // SECURITY: Find subscription by stripe_subscription_id (never infer)
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();

        if (!$subscription) {
            // Try to create subscription if it doesn't exist (from metadata if available)
            $subscription = $this->createSubscriptionFromInvoice($invoice, $eventId);
            if (!$subscription) {
                Log::warning('Stripe invoice payment succeeded but subscription not found', [
                    'event_id' => $eventId,
                    'invoice_id' => $invoice->id,
                    'stripe_subscription_id' => $stripeSubscriptionId,
                    'note' => 'Subscription may be created on next webhook event',
                ]);
                return;
            }
        }

        // Calculate new end date from invoice period
        $endsAt = $subscription->ends_at;
        if ($invoice->period_end) {
            $endsAt = \Carbon\Carbon::createFromTimestamp($invoice->period_end);
        }

        // Check if subscription was just activated (wasn't active before)
        $wasJustActivated = $subscription->status !== Subscription::STATUS_ACTIVE;
        
        // Activate or extend subscription
        $subscription->update([
            'status' => Subscription::STATUS_ACTIVE,
            'ends_at' => $endsAt,
            'stripe_customer_id' => $invoice->customer ?? $subscription->stripe_customer_id,
        ]);

        // Mark invoice as paid
        \App\Services\InvoiceService::markInvoiceAsPaid($subscription, \App\Models\Invoice::PAYMENT_METHOD_STRIPE);

        // Send payment success email if subscription was just activated (with cooldown)
        if ($wasJustActivated && !AutomationLog::wasSentRecently($subscription->tenant_id, AutomationLog::TYPE_PAYMENT_SUCCESS, null, 24)) {
            try {
                $primaryUser = $subscription->tenant->users()->wherePivot('role', 'admin')->first() 
                    ?? $subscription->tenant->users()->first();
                
                if ($primaryUser) {
                    Mail::to($primaryUser->email)->send(new PaymentSuccess($subscription->tenant, $subscription));
                    AutomationLog::logSent($subscription->tenant_id, AutomationLog::TYPE_PAYMENT_SUCCESS, null, [
                        'subscription_id' => $subscription->id,
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

        Log::info('Stripe subscription activated/extended', [
            'event_id' => $eventId,
            'subscription_id' => $subscription->id,
            'tenant_id' => $subscription->tenant_id,
            'invoice_id' => $invoice->id,
            'ends_at' => $endsAt,
            'status' => Subscription::STATUS_ACTIVE,
        ]);
    }

    /**
     * Handle invoice.payment_failed
     * 
     * SECURITY: Does NOT auto-disable tenant (grace period)
     * Marks subscription as past_due/expired but tenant remains functional
     * Super Admin can manually override if needed
     */
    protected function handleInvoicePaymentFailed($invoice, string $eventId)
    {
        Log::warning('Processing invoice.payment_failed', [
            'event_id' => $eventId,
            'invoice_id' => $invoice->id,
            'subscription_id' => $invoice->subscription ?? null,
            'attempt_count' => $invoice->attempt_count ?? 0,
            'next_payment_attempt' => $invoice->next_payment_attempt ?? null,
        ]);

        $stripeSubscriptionId = $invoice->subscription;

        if (!$stripeSubscriptionId) {
            Log::warning('Stripe invoice payment failed but missing subscription ID', [
                'event_id' => $eventId,
                'invoice_id' => $invoice->id,
            ]);
            return;
        }

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();

        if (!$subscription) {
            Log::warning('Stripe invoice payment failed for unknown subscription', [
                'event_id' => $eventId,
                'invoice_id' => $invoice->id,
                'stripe_subscription_id' => $stripeSubscriptionId,
            ]);
            return;
        }

        // Mark as expired BUT do NOT disable tenant (grace period)
        // Super Admin can manually override if needed
        // This prevents accidental service interruption
        $subscription->update([
            'status' => Subscription::STATUS_EXPIRED,
        ]);

        // Send payment failure warning email (with cooldown to avoid spam)
        if (!AutomationLog::wasSentRecently($subscription->tenant_id, AutomationLog::TYPE_PAYMENT_FAILURE, null, 72)) { // 72 hours = 3 days
            try {
                $primaryUser = $subscription->tenant->users()->wherePivot('role', 'admin')->first() 
                    ?? $subscription->tenant->users()->first();
                
                if ($primaryUser) {
                    Mail::to($primaryUser->email)->send(new PaymentFailureWarning($subscription->tenant, $subscription));
                    AutomationLog::logSent($subscription->tenant_id, AutomationLog::TYPE_PAYMENT_FAILURE, null, [
                        'subscription_id' => $subscription->id,
                        'recipient' => $primaryUser->email,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send payment failure warning email', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::warning('Stripe subscription marked as expired (grace period - tenant NOT disabled)', [
            'event_id' => $eventId,
            'subscription_id' => $subscription->id,
            'tenant_id' => $subscription->tenant_id,
            'invoice_id' => $invoice->id,
            'attempt_count' => $invoice->attempt_count ?? 0,
            'note' => 'Tenant remains active - Super Admin can override manually',
        ]);
    }

    /**
     * Create subscription from invoice if it doesn't exist
     * 
     * SECURITY: Uses invoice metadata to identify tenant (never infer)
     */
    protected function createSubscriptionFromInvoice($invoice, string $eventId): ?Subscription
    {
        // Try to get tenant/plan from invoice metadata
        $tenantId = $invoice->metadata->tenant_id ?? null;
        $planId = $invoice->metadata->plan_id ?? null;

        if (!$tenantId || !$planId) {
            // Try to get from subscription metadata if available
            $subscriptionId = $invoice->subscription;
            if ($subscriptionId) {
                try {
                    $stripeSubscription = \Stripe\Subscription::retrieve($subscriptionId);
                    $tenantId = $stripeSubscription->metadata->tenant_id ?? null;
                    $planId = $stripeSubscription->metadata->plan_id ?? null;
                } catch (\Exception $e) {
                    Log::warning('Could not retrieve Stripe subscription for metadata', [
                        'event_id' => $eventId,
                        'subscription_id' => $subscriptionId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        if (!$tenantId || !$planId) {
            Log::warning('Cannot create subscription from invoice - missing metadata', [
                'event_id' => $eventId,
                'invoice_id' => $invoice->id,
                'has_tenant_id' => !empty($tenantId),
                'has_plan_id' => !empty($planId),
            ]);
            return null;
        }

        $tenant = Tenant::find($tenantId);
        $plan = Plan::find($planId);

        if (!$tenant || !$plan) {
            Log::error('Cannot create subscription - invalid tenant or plan', [
                'event_id' => $eventId,
                'invoice_id' => $invoice->id,
                'tenant_id' => $tenantId,
                'plan_id' => $planId,
            ]);
            return null;
        }

        // Calculate end date from invoice period
        $endsAt = null;
        if ($invoice->period_end) {
            $endsAt = \Carbon\Carbon::createFromTimestamp($invoice->period_end);
        } else {
            // Fallback to billing cycle
            if ($plan->billing_cycle === 'monthly') {
                $endsAt = now()->addMonth();
            } elseif ($plan->billing_cycle === 'yearly') {
                $endsAt = now()->addYear();
            }
        }

            $subscription = Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'plan_name' => $plan->slug,
                'status' => Subscription::STATUS_ACTIVE,
                'max_clinics' => $plan->max_clinics,
                'max_users' => $plan->max_users,
                'max_screens' => $plan->max_screens ?? 1,
                'starts_at' => $invoice->period_start ? \Carbon\Carbon::createFromTimestamp($invoice->period_start) : now(),
                'ends_at' => $endsAt,
                'features' => $plan->features ?? [],
                'stripe_subscription_id' => $invoice->subscription,
                'stripe_customer_id' => $invoice->customer,
                'payment_method' => 'stripe',
            ]);

            // Generate invoice for Stripe payment
            \App\Services\InvoiceService::generateInvoice($subscription, $plan, \App\Models\Invoice::PAYMENT_METHOD_STRIPE);
            // Mark as paid (since this is triggered by payment_succeeded)
            \App\Services\InvoiceService::markInvoiceAsPaid($subscription, \App\Models\Invoice::PAYMENT_METHOD_STRIPE);

        Log::info('Created subscription from invoice', [
            'event_id' => $eventId,
            'subscription_id' => $subscription->id,
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'invoice_id' => $invoice->id,
        ]);

        return $subscription;
    }

    /**
     * Get or create Stripe customer for tenant
     */
    protected function getOrCreateStripeCustomer(Tenant $tenant): string
    {
        // Check if tenant already has a Stripe customer ID in any subscription
        $existingSubscription = Subscription::where('tenant_id', $tenant->id)
            ->whereNotNull('stripe_customer_id')
            ->first();

        if ($existingSubscription && $existingSubscription->stripe_customer_id) {
            return $existingSubscription->stripe_customer_id;
        }

        // Create new Stripe customer
        try {
            $customer = Customer::create([
                'email' => $tenant->email,
                'name' => $tenant->name,
                'metadata' => [
                    'tenant_id' => $tenant->id,
                ],
            ]);

            return $customer->id;
        } catch (\Exception $e) {
            Log::error('Failed to create Stripe customer', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant->id,
            ]);
            throw $e;
        }
    }
}

