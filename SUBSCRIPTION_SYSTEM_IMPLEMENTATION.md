# SaaS Subscription System Implementation

## Overview

A complete subscription management system for the multi-tenant Laravel queue management application. This system provides plan-based access control, clinic/screen limits, and manual activation (no payment gateway required).

## Database Schema

### Plans Table

Stores available subscription plans with limits:

- `id` - Primary key
- `name` - Plan name (e.g., "Basic", "Professional")
- `slug` - Unique slug (e.g., "basic", "professional")
- `description` - Plan description
- `price` - Monthly/yearly price
- `billing_cycle` - "monthly", "yearly", or "one_time"
- `max_clinics` - Maximum clinics allowed (-1 for unlimited)
- `max_users` - Maximum users allowed (-1 for unlimited)
- `max_screens` - Maximum display screens/second screens (-1 for unlimited) ← NEW
- `features` - JSON array of plan features
- `trial_days` - Trial period in days
- `is_active` - Whether plan is available
- `sort_order` - Display order
- `timestamps`

### Subscriptions Table

Tracks tenant subscriptions:

- `id` - Primary key
- `tenant_id` - Foreign key to tenants
- `plan_id` - Foreign key to plans ← NEW
- `plan_name` - Kept for backward compatibility
- `status` - "active", "cancelled", "expired", "trial"
- `max_clinics` - Clinic limit (copied from plan)
- `max_users` - User limit (copied from plan)
- `max_screens` - Screen limit (copied from plan) ← NEW
- `starts_at` - Subscription start date
- `ends_at` - Subscription end date (nullable)
- `cancelled_at` - Cancellation date (nullable)
- `features` - JSON array of features (copied from plan)
- `timestamps`

## Models

### Plan Model (`app/Models/Plan.php`)

**Methods:**
- `hasFeature(string $feature): bool` - Check if plan includes feature
- `hasUnlimitedClinics(): bool` - Check for unlimited clinics
- `hasUnlimitedUsers(): bool` - Check for unlimited users
- `hasUnlimitedScreens(): bool` - Check for unlimited screens ← NEW
- `findBySlug(string $slug): ?Plan` - Find plan by slug
- `scopeActive()` - Get only active plans
- `scopeOrdered()` - Order by sort_order and price

### Subscription Model (`app/Models/Subscription.php`)

**Methods:**
- `plan()` - Belongs to Plan
- `tenant()` - Belongs to Tenant
- `isActive(): bool` - Check if subscription is active
- `isExpired(): bool` - Check if subscription expired
- `hasFeature(string $feature): bool` - Check subscription/plan features
- `cancel(): void` - Cancel subscription
- `renew(\DateTime $newEndDate): void` - Renew subscription
- `expire(): void` - Mark as expired

**Auto-population:**
- On creation, automatically populates `plan_name`, `max_clinics`, `max_users`, `max_screens`, and `features` from the plan

## Middleware

### CheckSubscription

**Location:** `app/Http/Middleware/CheckSubscription.php`

Checks if tenant has active subscription or is on trial. Sets subscription limits in request.

**Usage:** Apply to routes requiring subscription:
```php
Route::middleware(['auth', 'tenant', 'subscription'])->group(function () {
    // Protected routes
});
```

### EnforceClinicLimit

**Location:** `app/Http/Middleware/EnforceClinicLimit.php` ← NEW

Enforces clinic creation limit based on subscription plan.

**Usage:**
```php
Route::middleware(['auth', 'tenant', 'subscription', 'clinic.limit'])->group(function () {
    Route::post('/clinics', [ClinicController::class, 'store']);
});
```

### EnforceScreenLimit

**Location:** `app/Http/Middleware/EnforceScreenLimit.php` ← NEW

Enforces screen limit for second screens/displays.

**Usage:**
```php
Route::middleware(['auth', 'tenant', 'subscription', 'screen.limit'])->group(function () {
    Route::post('/screens/open', [ScreenController::class, 'open']);
});
```

## Helpers

### SubscriptionHelper (`app/Helpers/SubscriptionHelper.php`)

**Static Methods:**
- `hasFeature(string $feature): bool` - Check if tenant has feature
- `getCurrentPlan(): ?Plan` - Get tenant's current plan
- `canCreateClinic(): bool` - Check if tenant can create more clinics
- `canAddUser(): bool` - Check if tenant can add more users
- `canOpenScreen(): bool` - Check if tenant can open more screens ← NEW
- `getMaxScreens(): int` - Get maximum screens allowed ← NEW

**Usage:**
```php
use App\Helpers\SubscriptionHelper;

if (SubscriptionHelper::canCreateClinic()) {
    // Allow clinic creation
}

if (SubscriptionHelper::canOpenScreen()) {
    // Allow screen opening
}
```

## Controllers

### PlanController (`app/Http/Controllers/PlanController.php`)

**Methods:**
- `index()` - Display available plans for tenant
- `activate(Request $request, Plan $plan)` - Activate plan for current tenant (manual)
- `renew(Request $request, Plan $plan)` - Renew current tenant's subscription
- `activateForTenant(Request $request, Tenant $tenant)` - Admin: Activate plan for specific tenant

## Routes

### Plan Routes (Tenant Self-Service)

```php
// View plans
Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');

// Activate plan (manual activation, no payment)
Route::post('/plans/{plan:slug}/activate', [PlanController::class, 'activate'])
    ->name('plans.activate')
    ->middleware(['auth', 'tenant', 'tenant.access']);

// Renew subscription
Route::post('/plans/{plan:slug}/renew', [PlanController::class, 'renew'])
    ->name('plans.renew')
    ->middleware(['auth', 'tenant', 'tenant.access', 'subscription']);
```

## Usage Examples

### Check Limits Before Creating Clinic

```php
use App\Helpers\SubscriptionHelper;

public function store(Request $request)
{
    if (!SubscriptionHelper::canCreateClinic()) {
        return back()->withErrors([
            'error' => 'You have reached your clinic limit. Please upgrade your plan.'
        ]);
    }
    
    // Create clinic...
}
```

### Check Screen Limits

```php
use App\Helpers\SubscriptionHelper;

public function openScreen(Request $request)
{
    if (!SubscriptionHelper::canOpenScreen()) {
        return response()->json([
            'success' => false,
            'message' => 'Screen limit reached. Please upgrade your plan.'
        ], 403);
    }
    
    // Open screen...
}
```

### Get Current Plan Limits

```php
$tenant = app('tenant');
$subscription = $tenant->subscription;
$plan = $subscription->plan;

$maxClinics = $plan->max_clinics; // -1 for unlimited
$maxUsers = $plan->max_users; // -1 for unlimited
$maxScreens = $plan->max_screens; // -1 for unlimited
```

## Default Plans

1. **Trial**
   - Price: Free
   - Max Clinics: 3
   - Max Users: 2
   - Max Screens: 1
   - Trial: 14 days

2. **Basic**
   - Price: $29/month
   - Max Clinics: 10
   - Max Users: 5
   - Max Screens: 2

3. **Professional**
   - Price: $99/month
   - Max Clinics: 50
   - Max Users: 20
   - Max Screens: 5

4. **Enterprise**
   - Price: Custom
   - Max Clinics: Unlimited (-1)
   - Max Users: Unlimited (-1)
   - Max Screens: Unlimited (-1)

## Migration Steps

1. Run migrations:
```bash
php artisan migrate
```

This will:
- Add `max_screens` to `plans` table
- Add `max_screens` to `subscriptions` table
- Update existing subscriptions with screen limits from plans
- Update default plan seeds with screen limits

2. Existing subscriptions will automatically get `max_screens` populated from their plans.

## Manual Activation

The system supports manual activation (no payment gateway). Tenants can:

1. View available plans at `/plans`
2. Select a plan
3. Activate it via POST to `/plans/{plan:slug}/activate`
4. Subscription is immediately active

For admin activation:
- Use `activateForTenant()` method
- Or update subscription directly in database

## Testing

```php
// Check if tenant can create clinic
$tenant = Tenant::find(1);
$canCreate = SubscriptionHelper::canCreateClinic();

// Check screen limit
$maxScreens = SubscriptionHelper::getMaxScreens();
$canOpen = SubscriptionHelper::canOpenScreen();

// Get current plan
$plan = SubscriptionHelper::getCurrentPlan();
```

## Notes

- All existing functionality is preserved
- Screen limits are tracked via session (enhance for production with database tracking)
- Clinic limits are enforced via middleware or helper checks
- Subscriptions automatically expire based on `ends_at` date
- Manual activation means no payment processing required

