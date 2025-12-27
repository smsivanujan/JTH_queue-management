# Stripe Subscription Integration Setup Guide

This document explains how to configure and use Stripe as an optional payment method for subscriptions in this Laravel multi-tenant SaaS.

## Overview

Stripe integration is **optional** and **additive** - it does not replace manual payment methods. Tenants can choose between:
- **Stripe (Card Payment)**: Automated subscription management via Stripe
- **Manual Payment**: Bank transfer or other manual methods (existing flow)

## Prerequisites

1. Stripe account (platform owner's account)
2. Stripe API keys (Publishable Key and Secret Key)
3. Stripe webhook endpoint configured

## Configuration Steps

### 1. Environment Variables

Add the following to your `.env` file:

```env
# Stripe Configuration
STRIPE_KEY=pk_test_...  # Your Stripe Publishable Key
STRIPE_SECRET=sk_test_...  # Your Stripe Secret Key
STRIPE_WEBHOOK_SECRET=whsec_...  # Your Stripe Webhook Signing Secret
```

**Note**: Use test keys (`pk_test_`, `sk_test_`) for development and live keys (`pk_live_`, `sk_live_`) for production.

### 2. Run Database Migration

```bash
php artisan migrate
```

This adds the following fields:
- `subscriptions.stripe_subscription_id` - Stripe subscription ID
- `subscriptions.stripe_customer_id` - Stripe customer ID
- `subscriptions.payment_method` - 'manual' or 'stripe'
- `plans.stripe_price_id` - Stripe Price ID for each plan

### 3. Configure Stripe Products and Prices

For each plan you want to enable Stripe payments:

1. Log in to your Stripe Dashboard
2. Go to **Products** → Create a new product
3. Set product name (e.g., "Basic Plan")
4. Add a price:
   - **Recurring**: Monthly or Yearly (based on plan's `billing_cycle`)
   - **Amount**: Match your plan's `price` field
5. Copy the **Price ID** (starts with `price_...`)
6. Update the plan in your database:

```sql
UPDATE plans SET stripe_price_id = 'price_xxxxx' WHERE slug = 'basic';
```

Or use a database seeder/migration to set these values.

### 4. Configure Stripe Webhook

1. Go to Stripe Dashboard → **Developers** → **Webhooks**
2. Click **Add endpoint**
3. Set endpoint URL: `https://yourdomain.com/stripe/webhook`
4. Select events to listen for:
   - `checkout.session.completed`
   - `customer.subscription.created`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
5. Copy the **Signing secret** (starts with `whsec_...`)
6. Add it to your `.env` as `STRIPE_WEBHOOK_SECRET`

## How It Works

### Checkout Flow

1. Tenant admin goes to Subscription page
2. Selects a plan with Stripe enabled (has `stripe_price_id`)
3. Clicks "Pay with Card (Stripe)" button
4. Redirected to Stripe Checkout
5. Completes payment
6. Redirected back to subscription page
7. Webhook processes subscription activation

### Webhook Processing

The webhook handler (`StripeController@webhook`) processes these events:

- **checkout.session.completed**: Confirms payment was successful
- **customer.subscription.created/updated**: Creates or updates subscription in database
- **customer.subscription.deleted**: Cancels subscription (grace period - doesn't immediately disable)
- **invoice.payment_succeeded**: Ensures subscription stays active
- **invoice.payment_failed**: Logs warning but doesn't immediately disable tenant

### Subscription Status Management

- **Active subscriptions**: Work normally regardless of payment method
- **Failed payments**: Logged but tenant is NOT immediately disabled (grace period)
- **Super Admin override**: Can manually activate/update subscriptions regardless of Stripe status
- **Manual payments**: Continue to work as before

## Important Notes

### Grace Period for Failed Payments

The system does **NOT** automatically disable tenants on payment failure. This is intentional:
- Prevents accidental service interruption
- Allows Super Admin to manually handle edge cases
- Gives tenants time to update payment methods

### Super Admin Controls

Super Admins can:
- Manually activate subscriptions (bypasses Stripe)
- Override subscription status
- Update subscription end dates
- Manage subscriptions regardless of Stripe status

### Tenant Isolation

- Each tenant gets a Stripe Customer ID
- Subscriptions are tenant-scoped
- No cross-tenant data leakage

## Testing

### Test Mode

1. Use Stripe test keys (`pk_test_`, `sk_test_`)
2. Use Stripe test cards:
   - Success: `4242 4242 4242 4242`
   - Decline: `4000 0000 0000 0002`
   - 3D Secure: `4000 0025 0000 3155`
3. Test webhooks using Stripe CLI:

```bash
stripe listen --forward-to localhost:8000/stripe/webhook
```

### Production Checklist

- [ ] Switch to live Stripe keys
- [ ] Configure production webhook endpoint
- [ ] Test checkout flow end-to-end
- [ ] Verify webhook signature validation
- [ ] Test payment failure scenarios
- [ ] Document Super Admin override procedures

## Troubleshooting

### Webhook Not Receiving Events

1. Check webhook endpoint URL is correct
2. Verify webhook secret in `.env`
3. Check Stripe Dashboard → Webhooks → Recent events
4. Review Laravel logs for webhook errors

### Subscription Not Activating

1. Check webhook logs in Stripe Dashboard
2. Review Laravel logs (`storage/logs/laravel.log`)
3. Verify plan has `stripe_price_id` set
4. Check subscription metadata in Stripe Dashboard

### Payment Method Not Showing

- Ensure `STRIPE_KEY` and `STRIPE_SECRET` are set in `.env`
- Verify plan has `stripe_price_id` configured
- Check `$stripeEnabled` variable in subscription view

## Support

For issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check Stripe Dashboard → Logs
3. Review webhook event details in Stripe Dashboard
4. Contact platform admin for Super Admin overrides

