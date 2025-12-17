# Debug Fix: Metrics Dashboard Access Issue

## Root Cause Identified

**Exact Cause**: The `/metrics` route is inside the middleware group that includes `subscription` middleware (`CheckSubscription`). When a tenant is selected (from session), the `CheckSubscription` middleware checks if that tenant has an active subscription. If the tenant doesn't have an active subscription or trial, it redirects to `/subscription/required`, blocking access to the metrics dashboard.

**Evidence**:
- Route is inside: `Route::middleware(['auth', 'tenant', 'tenant.access', 'subscription'])->group()`
- `CheckSubscription` middleware (lines 33-37) redirects if no active subscription
- Metrics dashboard is system-wide and shouldn't require subscription check

## Minimal Fix

The metrics route should bypass the subscription check since it's a system-wide admin dashboard that doesn't depend on tenant subscription status.

### Option 1: Exclude Metrics Route from Subscription Check (Recommended)

Modify `CheckSubscription` middleware to skip metrics routes:

```php
// In app/Http/Middleware/CheckSubscription.php
public function handle(Request $request, Closure $next): Response
{
    // Skip subscription check for system-wide metrics dashboard
    if ($request->routeIs('metrics.index')) {
        return $next($request);
    }
    
    $tenant = $request->get('tenant') ?? (app()->bound('tenant') ? app('tenant') : null);
    // ... rest of existing code
}
```

### Option 2: Move Metrics Route Outside Subscription Middleware

Move the metrics route to a separate middleware group that doesn't include subscription check:

```php
// In routes/web.php - move metrics route outside subscription middleware
Route::middleware(['auth', 'tenant', 'tenant.access'])->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::get('/metrics', [\App\Http\Controllers\MetricsController::class, 'index'])->name('metrics.index');
    });
});
```

## Current Behavior vs Expected Behavior

**Current**: User with tenant selected → Subscription middleware checks subscription → If no subscription → Redirect to subscription.required → Metrics dashboard blocked

**Expected**: Admin user → Should access metrics dashboard regardless of tenant subscription status

## Verification Steps

After applying fix:

1. Clear caches: `php artisan optimize:clear`
2. Test as admin user with tenant that has no subscription
3. Access `/metrics` - should work
4. Verify metrics are system-wide (not tenant-scoped)

## Additional Notes

- The controller already correctly bypasses tenant scoping for queries
- Navigation link exists in dashboard
- Route is properly registered
- Only the middleware chain needs adjustment

