# Pricing Plans Design & Configuration

This document describes the pricing plan structure for the Laravel multi-tenant queue management SaaS.

## Plan Structure

### 1. Trial Plan
- **Name:** Trial
- **Slug:** `trial`
- **Price:** $0 (Free)
- **Billing Cycle:** Monthly
- **Trial Period:** 14 days
- **Limits:**
  - Clinics: 3
  - Staff Members: 2
  - Display Screens: 1
- **Features:**
  - Basic queue management
  - Real-time updates
- **Stripe Price ID:** `null` (No Stripe integration)
- **Payment Method:** Free (no payment required)

### 2. Starter Plan
- **Name:** Starter
- **Slug:** `starter`
- **Price:** $29/month
- **Billing Cycle:** Monthly (yearly option: $290/year - 17% savings)
- **Trial Period:** None (use Trial plan first)
- **Limits:**
  - Clinics: 5
  - Staff Members: 5
  - Display Screens: 2
- **Features:**
  - Basic queue management
  - Real-time updates
  - Multi-service support
  - Basic analytics
- **Stripe Price ID:** Set after creating in Stripe Dashboard
- **Payment Method:** Stripe (optional) or Manual
- **Target Audience:** Small businesses and single locations

### 3. Pro Plan ⭐ (Recommended)
- **Name:** Pro
- **Slug:** `pro`
- **Price:** $99/month
- **Billing Cycle:** Monthly (yearly option: $990/year - 17% savings)
- **Trial Period:** None (use Trial plan first)
- **Limits:**
  - Clinics: 25
  - Staff Members: 15
  - Display Screens: 10
- **Features:**
  - Basic queue management
  - Real-time updates
  - Multi-service support
  - Advanced analytics
  - Custom branding
  - Priority support
- **Stripe Price ID:** Set after creating in Stripe Dashboard
- **Payment Method:** Stripe (optional) or Manual
- **Target Audience:** Growing businesses with multiple locations

### 4. Enterprise Plan
- **Name:** Enterprise
- **Slug:** `enterprise`
- **Price:** $0 (Custom pricing - contact sales)
- **Billing Cycle:** Monthly (custom)
- **Trial Period:** None
- **Limits:**
  - Clinics: Unlimited (-1)
  - Staff Members: Unlimited (-1)
  - Display Screens: Unlimited (-1)
- **Features:**
  - Basic queue management
  - Real-time updates
  - Multi-service support
  - Advanced analytics
  - Custom branding
  - API access
  - White label support
  - Dedicated account manager
  - Priority support
  - Custom integrations
- **Stripe Price ID:** `null` (Manual payment only)
- **Payment Method:** Manual only (no Stripe)
- **Target Audience:** Large organizations with unlimited needs

## Database Schema

Plans are stored in the `plans` table with the following structure:

```sql
- id: Primary key
- name: Plan name (e.g., "Starter", "Pro")
- slug: Unique identifier (e.g., "starter", "pro")
- description: Plan description text
- price: Decimal(10,2) - Monthly price in USD
- billing_cycle: String ('monthly' or 'yearly')
- max_clinics: Integer (-1 for unlimited)
- max_users: Integer (-1 for unlimited)
- max_screens: Integer (-1 for unlimited)
- features: JSON array of feature strings
- trial_days: Integer (0 if no trial)
- is_active: Boolean (active plans only)
- sort_order: Integer (display order)
- stripe_price_id: String (nullable) - Stripe Price ID
- created_at, updated_at: Timestamps
```

## Stripe Integration

### Setting Up Stripe Prices

1. **Create Products in Stripe Dashboard:**
   - Go to Stripe Dashboard → Products
   - Create a product for each paid plan (Starter, Pro)
   - Set product name to match plan name

2. **Create Prices:**
   - For each product, create a recurring price
   - Set billing interval: Monthly
   - Set amount: Match plan price (e.g., $29.00 for Starter)
   - Copy the Price ID (starts with `price_...`)

3. **Update Database:**
   ```sql
   UPDATE plans SET stripe_price_id = 'price_xxxxx' WHERE slug = 'starter';
   UPDATE plans SET stripe_price_id = 'price_xxxxx' WHERE slug = 'pro';
   ```

### Yearly Billing Support

For yearly billing, you have two options:

**Option 1: Separate Plans (Recommended)**
- Create separate plan records for yearly versions
- Set `billing_cycle` to 'yearly'
- Set `price` to yearly amount (e.g., 290.00 for Starter)
- Create separate Stripe Prices with yearly interval
- Update `stripe_price_id` accordingly

**Option 2: Single Plan with Multiple Prices**
- Keep single plan record
- Store multiple Stripe Price IDs (would require schema change)
- More complex implementation

Currently, the system supports **Option 1** (separate plans for monthly/yearly).

## Features Array

Features are stored as a JSON array. Example:

```json
[
  "basic_queue_management",
  "real_time_updates",
  "multi_service_support",
  "advanced_analytics",
  "custom_branding",
  "api_access",
  "priority_support"
]
```

### Feature Keys

- `basic_queue_management` - Core queue functionality
- `real_time_updates` - WebSocket real-time updates
- `multi_service_support` - Multiple service types
- `basic_analytics` - Basic usage statistics
- `advanced_analytics` - Advanced reporting and insights
- `custom_branding` - Custom logo and branding
- `api_access` - REST API access
- `white_label_support` - White label customization
- `priority_support` - Priority customer support
- `custom_integrations` - Custom integrations
- `dedicated_account_manager` - Dedicated account manager

## Seeding Plans

Run the PlanSeeder to populate default plans:

```bash
php artisan db:seed --class=PlanSeeder
```

Or run all seeders:

```bash
php artisan migrate:fresh --seed
```

## Recommended Plan Logic

The "Pro" plan (`slug = 'pro'`) is marked as the recommended plan in the UI. This is determined in the `SubscriptionController`:

```php
$recommendedPlanSlug = 'pro';
```

To change the recommended plan, update this value in `SubscriptionController@index`.

## Subscription Limits

Limits are enforced per tenant subscription:

1. **Max Clinics:** Maximum number of clinics/locations
2. **Max Users:** Maximum number of staff members
3. **Max Screens:** Maximum number of active display screens

Unlimited is represented as `-1` in the database.

## Pricing Display

Pricing is always pulled from the database, never hardcoded in Blade templates:

```blade
${{ number_format($plan->price, 2) }}
/ {{ ucfirst($plan->billing_cycle ?? 'month') }}
```

## Manual Payment Flow

1. Tenant selects a plan
2. Sees payment instructions (bank details, QR code)
3. Makes payment via bank transfer
4. Clicks "Notify Payment" button
5. Admin manually activates subscription

## Stripe Payment Flow

1. Tenant selects a plan with Stripe enabled
2. Clicks "Pay with Card (Stripe)" button
3. Redirected to Stripe Checkout
4. Completes payment
5. Webhook processes subscription activation
6. Subscription becomes active automatically

## Plan Comparison Table

| Feature | Trial | Starter | Pro | Enterprise |
|---------|-------|---------|-----|------------|
| Price | Free | $29/mo | $99/mo | Custom |
| Clinics | 3 | 5 | 25 | Unlimited |
| Staff | 2 | 5 | 15 | Unlimited |
| Screens | 1 | 2 | 10 | Unlimited |
| Real-time Updates | ✅ | ✅ | ✅ | ✅ |
| Analytics | ❌ | Basic | Advanced | Advanced |
| Custom Branding | ❌ | ❌ | ✅ | ✅ |
| API Access | ❌ | ❌ | ❌ | ✅ |
| Priority Support | ❌ | ❌ | ✅ | ✅ |
| Stripe Payment | ❌ | ✅ | ✅ | ❌ |

## Example Database Records

```sql
-- Trial Plan
INSERT INTO plans (name, slug, description, price, billing_cycle, max_clinics, max_users, max_screens, features, trial_days, is_active, sort_order, stripe_price_id, created_at, updated_at)
VALUES ('Trial', 'trial', '14-day free trial to explore all features', 0, 'monthly', 3, 2, 1, '["basic_queue_management","real_time_updates"]', 14, true, 0, NULL, NOW(), NOW());

-- Starter Plan
INSERT INTO plans (name, slug, description, price, billing_cycle, max_clinics, max_users, max_screens, features, trial_days, is_active, sort_order, stripe_price_id, created_at, updated_at)
VALUES ('Starter', 'starter', 'Perfect for small businesses and single locations', 29.00, 'monthly', 5, 5, 2, '["basic_queue_management","real_time_updates","multi_service_support","basic_analytics"]', 0, true, 1, NULL, NOW(), NOW());

-- Pro Plan (Recommended)
INSERT INTO plans (name, slug, description, price, billing_cycle, max_clinics, max_users, max_screens, features, trial_days, is_active, sort_order, stripe_price_id, created_at, updated_at)
VALUES ('Pro', 'pro', 'Ideal for growing businesses with multiple locations', 99.00, 'monthly', 25, 15, 10, '["basic_queue_management","real_time_updates","multi_service_support","advanced_analytics","custom_branding","priority_support"]', 0, true, 2, NULL, NOW(), NOW());

-- Enterprise Plan
INSERT INTO plans (name, slug, description, price, billing_cycle, max_clinics, max_users, max_screens, features, trial_days, is_active, sort_order, stripe_price_id, created_at, updated_at)
VALUES ('Enterprise', 'enterprise', 'For large organizations with unlimited needs', 0, 'monthly', -1, -1, -1, '["basic_queue_management","real_time_updates","multi_service_support","advanced_analytics","custom_branding","api_access","white_label_support","dedicated_account_manager","priority_support","custom_integrations"]', 0, true, 3, NULL, NOW(), NOW());
```

## Updating Plans

To update plan pricing or features:

1. Update the `PlanSeeder` seeder file
2. Run `php artisan db:seed --class=PlanSeeder` (uses `updateOrCreate`, so existing plans are updated)
3. Or manually update in database:
   ```sql
   UPDATE plans SET price = 39.00 WHERE slug = 'starter';
   ```

## Notes

- Prices are stored in USD (decimal with 2 decimal places)
- All prices should be updated in the database, never hardcoded in views
- Stripe Price IDs must be set after creating prices in Stripe Dashboard
- Enterprise plan is manual payment only (no Stripe)
- Trial plan is free and requires no payment

