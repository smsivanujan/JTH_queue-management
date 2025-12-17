# SaaS Subscription System Documentation

## Overview

A comprehensive subscription management system for the multi-tenant queue management application. This system provides plan-based access control, expiry management, and feature gating without requiring payment gateway integration.

## Database Schema

### Plans Table

Stores available subscription plans:

```sql
plans
- id
- name (e.g., "Basic", "Professional")
- slug (e.g., "basic", "professional")
- description
- price (decimal)
- billing_cycle (monthly/yearly)
- max_clinics (integer, -1 for unlimited)
- max_users (integer, -1 for unlimited)
- features (JSON array)
- trial_days (integer)
- is_active (boolean)
- sort_order (integer)
- timestamps
```

### Subscriptions Table (Updated)

Now includes `plan_id` foreign key:

```sql
subscriptions
- id
- tenant_id (FK)
- plan_id (FK) ← NEW
- plan_name (kept for backward compatibility)
- status (active, cancelled, expired, trial)
- max_clinics
- max_users
- starts_at
- ends_at
- cancelled_at
- features (JSON)
- timestamps
```

## Models

### Plan Model

**Location:** `app/Models/Plan.php`

**Key Methods:**
- `hasFeature(string $feature): bool` - Check if plan includes feature
- `hasUnlimitedClinics(): bool` - Check for unlimited clinics
- `hasUnlimitedUsers(): bool` - Check for unlimited users
- `findBySlug(string $slug): ?Plan` - Find plan by slug
- `scopeActive()` - Get only active plans
- `scopeOrdered()` - Order by sort_order and price

**Relationships:**
- `subscriptions()` - Has many subscriptions

### Subscription Model (Updated)

**Location:** `app/Models/Subscription.php`

**New Methods:**
- `plan()` - Belongs to Plan
- `hasFeature(string $feature): bool` - Check subscription/plan features
- `renew(\DateTime $newEndDate): void` - Renew subscription
- `expire(): void` - Mark as expired

**Auto-population:**
- On creation, automatically populates `plan_name`, `max_clinics`, `max_users`, and `features` from the plan if not provided

### Tenant Model (Updated)

**New Methods:**
- `hasFeature(string $feature): bool` - Check if tenant has feature access
- `getCurrentPlan(): ?Plan` - Get current active plan

## Middleware

### CheckSubscriptionExpiry

**Location:** `app/Http/Middleware/CheckSubscriptionExpiry.php`

**Purpose:** Automatically expires subscriptions that have passed their end date

**Usage:**
```php
Route::middleware(['subscription.expiry'])->group(function () {
    // Routes that need expiry checking
});
```

### CheckPlanFeature

**Location:** `app/Http/Middleware/CheckPlanFeature.php`

**Purpose:** Restrict access to routes based on plan features

**Usage:**
```php
// Require single feature
Route::middleware('plan.feature:analytics')->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index']);
});

// Require any of multiple features
Route::middleware('plan.feature:analytics,api_access')->group(function () {
    Route::get('/advanced', [AdvancedController::class, 'index']);
});
```

**Behavior:**
- Checks if tenant has active subscription
- Verifies subscription has at least one of the required features
- Returns 403 if feature not available
- Redirects to subscription page if no active subscription

## Features System

### Available Features

Features are stored as JSON arrays in plans and subscriptions:

- `basic_queue_management` - Basic queue operations
- `real_time_updates` - Real-time queue synchronization
- `analytics` - Analytics and reporting
- `custom_branding` - Custom branding options
- `api_access` - API access
- `priority_support` - Priority customer support

### Checking Features

**In Controllers:**
```php
if ($tenant->hasFeature('analytics')) {
    // Show analytics
}
```

**In Blade Templates:**
```blade
@if($tenant->hasFeature('analytics'))
    <a href="/analytics">Analytics</a>
@endif
```

**Using Helper:**
```php
use App\Helpers\SubscriptionHelper;

if (SubscriptionHelper::hasFeature('analytics')) {
    // Feature available
}
```

**In Routes:**
```php
Route::middleware('plan.feature:analytics')->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index']);
});
```

## Default Plans

The system includes 4 default plans:

1. **Trial** - 14-day free trial
   - 3 clinics, 2 users
   - Basic queue management

2. **Basic** - $29/month
   - 10 clinics, 5 users
   - Basic queue management, real-time updates

3. **Professional** - $99/month
   - 50 clinics, 20 users
   - All Basic features + Analytics + Custom branding

4. **Enterprise** - Custom pricing
   - Unlimited clinics and users
   - All features + API access + Priority support

## Manual Activation

Since there's no payment gateway, subscriptions are activated manually:

### Using PlanController

```php
// Activate plan for tenant
POST /plans/activate/{tenant}
{
    "plan_id": 2,
    "ends_at": "2025-02-22" // Optional, defaults to billing cycle
}
```

### Using Tinker

```php
use App\Models\Plan;
use App\Models\Tenant;
use App\Services\TenantService;

$tenant = Tenant::find(1);
$plan = Plan::findBySlug('professional');
$tenantService = app(TenantService::class);

$subscription = $tenantService->createSubscription($tenant, 'professional');
```

### Direct Database

```php
$subscription = Subscription::create([
    'tenant_id' => $tenant->id,
    'plan_id' => $plan->id,
    'plan_name' => $plan->slug,
    'status' => Subscription::STATUS_ACTIVE,
    'starts_at' => now(),
    'ends_at' => now()->addMonth(),
]);
```

## Expiry Management

### Automatic Expiry

The `CheckSubscriptionExpiry` middleware automatically:
- Checks all active subscriptions
- Expires subscriptions where `ends_at` has passed
- Logs expiry events

### Manual Expiry

```php
$subscription->expire();
```

### Renewal

```php
$subscription->renew(now()->addMonth());
```

## Usage Examples

### Restrict Route by Feature

```php
// Only accessible with analytics feature
Route::middleware(['auth', 'tenant', 'subscription', 'plan.feature:analytics'])->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index']);
});
```

### Check Feature in Controller

```php
public function index()
{
    $tenant = app('tenant');
    
    if (!$tenant->hasFeature('analytics')) {
        return redirect()->route('subscription.required')
            ->with('error', 'Analytics feature requires Professional plan or higher.');
    }
    
    // Show analytics
}
```

### Check Limits

```php
use App\Helpers\SubscriptionHelper;

if (!SubscriptionHelper::canCreateClinic()) {
    return back()->withErrors(['You have reached your clinic limit. Please upgrade your plan.']);
}

// Create clinic
```

### Display Plan Information

```php
$plan = $tenant->getCurrentPlan();
echo $plan->name; // "Professional"
echo $plan->price; // 99.00
echo $plan->max_clinics; // 50
```

## Migration Steps

1. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

2. **Seed Plans (if not already seeded):**
   ```bash
   php artisan db:seed --class=PlanSeeder
   ```

3. **Update Existing Subscriptions:**
   The migration `2025_01_22_000004_populate_plan_id_in_subscriptions.php` will automatically populate `plan_id` from `plan_name`.

## Best Practices

1. **Always check subscription status** before checking features
2. **Use middleware** for route-level feature restrictions
3. **Use helper methods** for limit checking
4. **Log subscription changes** for audit purposes
5. **Set appropriate expiry dates** when creating subscriptions
6. **Use plan features** instead of hardcoding limits

## API Endpoints

### Get All Plans
```
GET /plans
```

### Activate Plan (Admin)
```
POST /plans/activate/{tenant}
Body: { plan_id, ends_at? }
```

### Renew Subscription
```
POST /subscriptions/{subscription}/renew
Body: { ends_at }
```

## Testing

### Test Feature Access

```php
$tenant = Tenant::factory()->create();
$plan = Plan::findBySlug('professional');
$subscription = Subscription::factory()->create([
    'tenant_id' => $tenant->id,
    'plan_id' => $plan->id,
    'status' => Subscription::STATUS_ACTIVE,
]);

$this->assertTrue($tenant->hasFeature('analytics'));
$this->assertFalse($tenant->hasFeature('api_access'));
```

### Test Expiry

```php
$subscription = Subscription::factory()->create([
    'ends_at' => now()->subDay(),
    'status' => Subscription::STATUS_ACTIVE,
]);

$subscription->expire();
$this->assertEquals(Subscription::STATUS_EXPIRED, $subscription->status);
```

## Troubleshooting

### Subscription Not Found
- Ensure tenant has an active subscription
- Check subscription status
- Verify `ends_at` date hasn't passed

### Feature Not Available
- Check if plan includes the feature
- Verify subscription is active
- Check feature name spelling

### Limits Not Enforced
- Verify middleware is applied
- Check plan limits are set correctly
- Ensure subscription is active

