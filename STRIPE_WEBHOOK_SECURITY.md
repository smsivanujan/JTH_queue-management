# Stripe Webhook Security Documentation

## Overview

The Stripe webhook handler is a **critical security component** that processes payment events from Stripe. This document explains the security measures and implementation details.

## Security Principles

### 1. Signature Verification (CRITICAL)

**The webhook endpoint has NO authentication or tenant middleware. Signature verification is the ONLY security mechanism.**

- All webhook requests MUST include a valid `Stripe-Signature` header
- The signature is verified using the webhook secret (`STRIPE_WEBHOOK_SECRET`)
- Invalid signatures result in 400 response and the event is logged but NOT processed

```php
$event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
```

### 2. Webhook as Source of Truth

**The webhook is the ONLY source of truth for payment status. Frontend payment status is NEVER trusted.**

- All subscription state changes MUST come from webhook events
- Frontend success pages are informational only
- Subscription activation happens via webhook, not redirect

### 3. Tenant Identification via Metadata

**Tenants are ALWAYS identified using metadata.tenant_id. Never infer tenant indirectly.**

```php
// ✅ CORRECT - Use metadata
$tenantId = $session->metadata->tenant_id ?? null;

// ❌ WRONG - Never infer from customer email, etc.
$customer = Customer::retrieve($customerId);
$tenant = Tenant::where('email', $customer->email)->first();
```

### 4. Grace Period for Failures

**Tenants are NEVER auto-disabled on payment failure. Grace period is enforced.**

- Payment failures mark subscription as `expired` but tenant remains functional
- Super Admin can manually override any subscription
- This prevents accidental service interruption

### 5. Event Handling

**Only specific events are processed. All others are logged but ignored.**

Handled events:
- `checkout.session.completed` - Confirms payment successful
- `invoice.payment_succeeded` - Activates/extends subscription
- `invoice.payment_failed` - Marks expired (grace period)
- `customer.subscription.deleted` - Marks cancelled (grace period)

Unhandled events are logged with `(intentionally ignored)` for auditing.

## Webhook Endpoint

### Route Configuration

```php
Route::post('/stripe/webhook', [StripeController::class, 'webhook'])
    ->name('stripe.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
```

**Important:**
- No auth middleware
- No tenant middleware
- CSRF disabled (Stripe signature verification replaces it)
- Publicly accessible endpoint

### Request Flow

1. Stripe sends POST request to `/stripe/webhook`
2. Signature verification happens FIRST
3. Event is parsed and validated
4. Event type is checked (only specific types processed)
5. Handler method is called
6. Subscription is updated
7. Response 200 is returned (or 500 on error)

## Event Handlers

### checkout.session.completed

**Purpose:** Confirms payment was successful

**Actions:**
- Validates metadata (tenant_id, plan_id)
- Logs successful checkout
- Does NOT create subscription (handled by invoice.payment_succeeded)

**Security:**
- Uses `session->metadata->tenant_id` to identify tenant
- Validates tenant and plan exist
- Logs all actions for audit trail

### invoice.payment_succeeded

**Purpose:** Activates or extends subscription

**Actions:**
- Finds subscription by `stripe_subscription_id`
- Creates subscription if missing (from metadata)
- Updates status to `active`
- Sets `ends_at` from invoice period
- Updates `stripe_customer_id`

**Security:**
- Primary activation event
- Never auto-disables on success
- Extends subscription period automatically

### invoice.payment_failed

**Purpose:** Marks subscription as expired (grace period)

**Actions:**
- Finds subscription by `stripe_subscription_id`
- Updates status to `expired`
- **DOES NOT disable tenant** (grace period)
- Logs warning for admin review

**Security:**
- Never auto-disables tenant
- Super Admin can manually override
- Prevents accidental service interruption

### customer.subscription.deleted

**Purpose:** Handles subscription cancellation

**Actions:**
- Finds subscription by `stripe_subscription_id`
- Updates status to `cancelled`
- Sets `cancelled_at` timestamp
- **DOES NOT disable tenant** (grace period)

**Security:**
- Never auto-disables tenant
- Super Admin can manually override
- Grace period allows admin intervention

## Subscription Status Mapping

### Status Values

- `active` - Subscription is active and paid
- `cancelled` - Subscription cancelled (grace period)
- `expired` - Subscription expired/past due (grace period)
- `trial` - Trial period (not used by Stripe)

### Status Updates

```php
// Payment succeeded → active
$subscription->update(['status' => Subscription::STATUS_ACTIVE]);

// Payment failed → expired (grace period)
$subscription->update(['status' => Subscription::STATUS_EXPIRED]);

// Subscription deleted → cancelled (grace period)
$subscription->update(['status' => Subscription::STATUS_CANCELLED]);
```

## Logging

All webhook events are logged for debugging and audit:

### Log Levels

- **INFO:** Normal operations (checkout completed, payment succeeded)
- **WARNING:** Missing metadata, unknown subscriptions
- **ERROR:** Invalid signatures, missing data, processing failures

### Log Data

Each log entry includes:
- Event ID (Stripe event ID)
- Event type
- Subscription ID (local)
- Tenant ID
- Stripe subscription ID
- Relevant metadata

### Example Log Entries

```php
Log::info('Stripe subscription activated/extended', [
    'event_id' => $eventId,
    'subscription_id' => $subscription->id,
    'tenant_id' => $subscription->tenant_id,
    'invoice_id' => $invoice->id,
]);
```

## Error Handling

### Signature Verification Failure

- Returns 400 status
- Logs error with signature details
- Event is NOT processed

### Missing Metadata

- Logs warning
- Event is NOT processed
- Returns 200 to prevent Stripe retries

### Invalid Tenant/Plan

- Logs error
- Event is NOT processed
- Returns 200 to prevent Stripe retries

### Processing Exception

- Logs error with full trace
- Returns 500 (causes Stripe to retry)
- Or returns 200 (prevents retries) depending on error type

## Testing

### Local Testing with Stripe CLI

```bash
# Install Stripe CLI
stripe listen --forward-to localhost:8000/stripe/webhook

# Trigger test events
stripe trigger checkout.session.completed
stripe trigger invoice.payment_succeeded
stripe trigger invoice.payment_failed
```

### Test Checklist

- [ ] Signature verification works correctly
- [ ] Invalid signatures are rejected
- [ ] Metadata is used correctly
- [ ] Subscriptions are created on first payment
- [ ] Subscriptions are extended on renewal
- [ ] Payment failures don't disable tenants
- [ ] Cancellations don't disable tenants
- [ ] All events are logged
- [ ] Super Admin can override status

## Production Checklist

- [ ] Webhook secret is set in `.env`
- [ ] Webhook endpoint is configured in Stripe Dashboard
- [ ] All required events are selected
- [ ] SSL certificate is valid (required by Stripe)
- [ ] Logging is configured and monitored
- [ ] Alerts are set up for webhook failures
- [ ] Super Admin override procedures are documented

## Security Best Practices

1. **Never trust frontend status** - Always verify via webhook
2. **Always verify signature** - Never skip signature verification
3. **Use metadata for tenant ID** - Never infer tenant
4. **Log everything** - All events should be logged
5. **Grace periods** - Never auto-disable on failure
6. **Admin overrides** - Super Admin can always override
7. **Monitor logs** - Set up alerts for failures
8. **Test thoroughly** - Test all event types before production

## Common Issues

### Webhook Not Receiving Events

1. Check webhook endpoint URL in Stripe Dashboard
2. Verify webhook secret in `.env`
3. Check Stripe Dashboard → Webhooks → Recent events
4. Review Laravel logs for errors

### Signature Verification Fails

1. Verify `STRIPE_WEBHOOK_SECRET` matches Stripe Dashboard
2. Check webhook endpoint URL is correct
3. Ensure raw request body is used (not parsed JSON)
4. Verify webhook is not behind a proxy that modifies headers

### Subscription Not Activating

1. Check `invoice.payment_succeeded` event was received
2. Verify metadata contains `tenant_id` and `plan_id`
3. Check subscription was created in database
4. Review logs for errors

### Payment Failed But Tenant Still Active

This is **expected behavior** (grace period). To manually disable:
1. Super Admin logs in
2. Finds tenant subscription
3. Manually updates status if needed
4. Or uses platform admin tools to override

