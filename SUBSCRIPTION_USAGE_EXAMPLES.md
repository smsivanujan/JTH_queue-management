# Subscription System Usage Examples

## Basic Usage

### Check if Tenant Has Feature

```php
// In Controller
$tenant = app('tenant');
if ($tenant->hasFeature('analytics')) {
    // Show analytics
}

// Using Helper
use App\Helpers\SubscriptionHelper;
if (SubscriptionHelper::hasFeature('analytics')) {
    // Feature available
}
```

### Restrict Route by Feature

```php
// routes/web.php
Route::middleware(['auth', 'tenant', 'subscription', 'plan.feature:analytics'])->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index']);
    Route::get('/reports', [ReportsController::class, 'index']);
});

// Require multiple features (any one)
Route::middleware(['auth', 'tenant', 'subscription', 'plan.feature:analytics,api_access'])->group(function () {
    Route::get('/advanced', [AdvancedController::class, 'index']);
});
```

### Check Limits Before Action

```php
use App\Helpers\SubscriptionHelper;

public function createClinic(Request $request)
{
    if (!SubscriptionHelper::canCreateClinic()) {
        return back()->withErrors([
            'limit' => 'You have reached your clinic limit. Please upgrade your plan.'
        ]);
    }
    
    // Create clinic
    Clinic::create([...]);
}
```

### Display Plan Information

```php
// In Controller
$tenant = app('tenant');
$plan = $tenant->getCurrentPlan();

return view('dashboard', [
    'plan' => $plan,
    'maxClinics' => $plan->max_clinics,
    'currentClinics' => $tenant->clinics()->count(),
]);
```

### In Blade Templates

```blade
@if($tenant->hasFeature('analytics'))
    <a href="/analytics" class="nav-link">Analytics</a>
@endif

@if($tenant->hasFeature('custom_branding'))
    <a href="/settings/branding">Customize Branding</a>
@endif

@php
    $plan = $tenant->getCurrentPlan();
    $subscription = $tenant->subscription;
@endphp

<div class="plan-info">
    <h3>Current Plan: {{ $plan->name }}</h3>
    <p>Price: ${{ number_format($plan->price, 2) }}/month</p>
    <p>Clinics: {{ $tenant->clinics()->count() }} / {{ $plan->max_clinics == -1 ? 'Unlimited' : $plan->max_clinics }}</p>
    <p>Users: {{ $tenant->users()->count() }} / {{ $plan->max_users == -1 ? 'Unlimited' : $plan->max_users }}</p>
    
    @if($subscription->ends_at)
        <p>Expires: {{ $subscription->ends_at->format('M d, Y') }}</p>
    @endif
</div>
```

## Manual Subscription Management

### Create Subscription

```php
use App\Models\Plan;
use App\Models\Tenant;
use App\Services\TenantService;

$tenant = Tenant::find(1);
$tenantService = app(TenantService::class);

// Create subscription with plan slug
$subscription = $tenantService->createSubscription($tenant, 'professional');

// Or with custom end date
$endsAt = now()->addYear();
$subscription = $tenantService->createSubscription($tenant, 'enterprise', $endsAt);
```

### Activate Plan for Tenant

```php
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;

$tenant = Tenant::find(1);
$plan = Plan::findBySlug('professional');

// Cancel existing subscriptions
$tenant->subscriptions()
    ->where('status', Subscription::STATUS_ACTIVE)
    ->update([
        'status' => Subscription::STATUS_CANCELLED,
        'cancelled_at' => now(),
    ]);

// Create new subscription
$subscription = Subscription::create([
    'tenant_id' => $tenant->id,
    'plan_id' => $plan->id,
    'plan_name' => $plan->slug,
    'status' => Subscription::STATUS_ACTIVE,
    'max_clinics' => $plan->max_clinics,
    'max_users' => $plan->max_users,
    'starts_at' => now(),
    'ends_at' => now()->addMonth(),
    'features' => $plan->features,
]);
```

### Renew Subscription

```php
$subscription = Subscription::find(1);
$subscription->renew(now()->addMonth());
```

### Cancel Subscription

```php
$subscription = Subscription::find(1);
$subscription->cancel();
```

## Feature Gating Examples

### Controller Method

```php
public function analytics()
{
    $tenant = app('tenant');
    
    if (!$tenant->hasFeature('analytics')) {
        return redirect()->route('subscription.required')
            ->with('error', 'Analytics requires Professional plan or higher.');
    }
    
    // Show analytics
    return view('analytics.index');
}
```

### API Endpoint

```php
Route::middleware(['auth', 'tenant', 'subscription', 'plan.feature:api_access'])->group(function () {
    Route::get('/api/v1/queues', [ApiController::class, 'getQueues']);
});
```

### Conditional Feature Display

```php
// In Controller
$features = [
    'analytics' => $tenant->hasFeature('analytics'),
    'api_access' => $tenant->hasFeature('api_access'),
    'custom_branding' => $tenant->hasFeature('custom_branding'),
];

return view('settings', compact('features'));
```

## Limit Enforcement

### Before Creating Clinic

```php
use App\Helpers\SubscriptionHelper;

public function store(Request $request)
{
    if (!SubscriptionHelper::canCreateClinic()) {
        return response()->json([
            'error' => 'Clinic limit reached. Please upgrade your plan.'
        ], 403);
    }
    
    Clinic::create($request->validated());
}
```

### Before Adding User

```php
use App\Helpers\SubscriptionHelper;

public function addUser(Request $request)
{
    if (!SubscriptionHelper::canAddUser()) {
        return back()->withErrors([
            'user' => 'User limit reached. Please upgrade your plan.'
        ]);
    }
    
    // Add user
}
```

## Plan Management

### Get All Plans

```php
use App\Models\Plan;

$plans = Plan::active()->ordered()->get();
```

### Find Plan by Slug

```php
$plan = Plan::findBySlug('professional');
```

### Check Plan Features

```php
$plan = Plan::find(1);

if ($plan->hasFeature('analytics')) {
    // Plan includes analytics
}
```

## Subscription Status Checks

### Check if Active

```php
$subscription = $tenant->subscription;

if ($subscription && $subscription->isActive()) {
    // Subscription is active
}
```

### Check if Expired

```php
if ($subscription->isExpired()) {
    // Handle expired subscription
}
```

### Check Trial Status

```php
if ($tenant->isOnTrial()) {
    $daysLeft = $tenant->trial_ends_at->diffInDays(now());
    // Show trial countdown
}
```

## Complete Example: Feature-Gated Analytics Page

```php
// routes/web.php
Route::middleware(['auth', 'tenant', 'subscription', 'plan.feature:analytics'])->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
});

// AnalyticsController.php
class AnalyticsController extends Controller
{
    public function index()
    {
        $tenant = app('tenant');
        $plan = $tenant->getCurrentPlan();
        
        // Additional check (redundant but safe)
        if (!$tenant->hasFeature('analytics')) {
            abort(403, 'Analytics feature not available on your plan');
        }
        
        return view('analytics.index', [
            'tenant' => $tenant,
            'plan' => $plan,
        ]);
    }
}
```

## Scheduled Tasks (Optional)

Add to `app/Console/Kernel.php` to auto-expire subscriptions:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        \App\Models\Subscription::where('status', '!=', \App\Models\Subscription::STATUS_EXPIRED)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->each(function ($subscription) {
                $subscription->expire();
            });
    })->daily();
}
```

