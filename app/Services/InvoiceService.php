<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;

class InvoiceService
{
    /**
     * Generate invoice for a subscription
     */
    public static function generateInvoice(Subscription $subscription, Plan $plan, string $paymentMethod = null): Invoice
    {
        // Skip invoice generation for free plans (trial)
        if ($plan->price == 0) {
            // Still create invoice for record keeping, but mark as paid
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'tenant_id' => $subscription->tenant_id,
                'subscription_id' => $subscription->id,
                'amount' => 0,
                'currency' => 'USD',
                'status' => Invoice::STATUS_PAID,
                'payment_method' => null,
                'issued_at' => now(),
                'paid_at' => now(),
                'metadata' => [
                    'plan_name' => $plan->name,
                    'plan_slug' => $plan->slug,
                    'billing_cycle' => $plan->billing_cycle,
                    'is_trial' => true,
                ],
            ]);
            
            return $invoice;
        }

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'tenant_id' => $subscription->tenant_id,
            'subscription_id' => $subscription->id,
            'amount' => $plan->price,
            'currency' => 'USD',
            'status' => Invoice::STATUS_PENDING,
            'payment_method' => $paymentMethod,
            'issued_at' => now(),
            'paid_at' => null,
            'metadata' => [
                'plan_name' => $plan->name,
                'plan_slug' => $plan->slug,
                'billing_cycle' => $plan->billing_cycle,
                'subscription_starts_at' => $subscription->starts_at?->toDateString(),
                'subscription_ends_at' => $subscription->ends_at?->toDateString(),
            ],
        ]);

        return $invoice;
    }

    /**
     * Mark invoice as paid
     */
    public static function markInvoiceAsPaid(Subscription $subscription, string $paymentMethod = null): ?Invoice
    {
        // Find the pending invoice for this subscription
        $invoice = Invoice::where('subscription_id', $subscription->id)
            ->where('status', Invoice::STATUS_PENDING)
            ->latest()
            ->first();

        if ($invoice) {
            $invoice->markAsPaid($paymentMethod);
        }

        return $invoice;
    }
}

