# Tenant Identification Fix - Single Source of Truth

**Date:** December 18, 2025  
**Purpose:** Fix recurring 403 "Tenant not identified" errors by using `current_tenant_id` as the ONLY source of truth

---

## Changes Made

### 1. Rewritten IdentifyTenant Middleware

**File:** `app/Http/Middleware/IdentifyTenant.php`

**Key Changes:**
- ✅ **Removed** all alternative tenant resolution methods:
  - ❌ Subdomain checking
  - ❌ Custom domain checking
  - ❌ Route parameter checking
  - ❌ Session-based tenant resolution
- ✅ **Uses ONLY** `auth()->user()->current_tenant_id` as source of truth
- ✅ Runs **AFTER** authentication (requires `auth()->check()`)
- ✅ Verifies tenant is active before using it
- ✅ Registers tenant in app container: `app()->instance('tenant', $tenant)`

**Code:**
```php
public function handle(Request $request, Closure $next): Response
{
    // Ensure user is authenticated (this middleware should run AFTER 'auth' middleware)
    if (!auth()->check()) {
        return $next($request);
    }

    $user = auth()->user();
    $tenant = null;

    // SOURCE OF TRUTH: current_tenant_id from authenticated user
    if ($user->current_tenant_id) {
        $tenant = Tenant::find($user->current_tenant_id);
        
        // Verify tenant exists and is active
        if ($tenant && !$tenant->is_active) {
            $tenant = null; // Don't use inactive tenants
        }
    }

    // Set tenant in request and service container (if found)
    if ($tenant) {
        $request->merge(['tenant' => $tenant]);
        app()->instance('tenant', $tenant);
        app()->instance('tenant_id', $tenant->id);
    }

    return $next($request);
}
```

---

### 2. Removed Global Middleware Registration

**File:** `bootstrap/app.php`

**Before:**
```php
$middleware->web(append: [
    \App\Http\Middleware\IdentifyTenant::class,  // ❌ Removed
    \App\Http\Middleware\CheckSubscriptionExpiry::class,
]);
```

**After:**
```php
$middleware->web(append: [
    \App\Http\Middleware\CheckSubscriptionExpiry::class,  // ✅ Kept (doesn't identify tenant)
]);
```

**Rationale:** `IdentifyTenant` should ONLY run in tenant route groups where `auth` middleware has already run, ensuring `current_tenant_id` is available.

---

### 3. Route Group Verification

#### Platform Routes (NO tenant middleware)
```php
Route::middleware('auth')->group(function () {
    Route::get('/platform/dashboard', ...);  // ✅ No tenant middleware
    Route::get('/tenant/select', ...);       // ✅ No tenant middleware
    Route::post('/tenant/switch/{tenant:slug}', ...);  // ✅ No tenant middleware
});
```

#### Tenant Routes (REQUIRE tenant middleware)
```php
Route::middleware(['auth', 'tenant', 'tenant.access', 'subscription'])->group(function () {
    // All tenant-scoped routes
    Route::get('/dashboard', ...);
    Route::resource('clinic', ...);      // ✅ Includes /clinic/{id}/edit
    Route::resource('staff', ...);       // ✅ Includes /staff/{id}/edit
    Route::get('/queues/{clinic}', ...); // ✅ Uses route model binding
    // ... all other tenant routes
});
```

**Middleware Order:**
1. `auth` - Authenticates user
2. `tenant` (IdentifyTenant) - Identifies tenant from `current_tenant_id`
3. `tenant.access` (EnsureTenantAccess) - Verifies tenant access
4. `subscription` - Checks subscription status

---

## Verification

### ✅ Controllers and Helpers

**Controllers:**
- ✅ Only retrieve tenant from app container: `app()->bound('tenant') ? app('tenant') : null`
- ✅ No tenant resolution logic in controllers
- ✅ All tenant-scoped controllers properly check for tenant existence

**Helpers:**
- ✅ `TenantHelper::current()` - Only reads from app container
- ✅ `SubscriptionHelper` - Only reads from app container
- ✅ No tenant resolution logic in helpers

### ✅ Route Model Binding

**File:** `app/Providers/AppServiceProvider.php`

```php
Route::bind('clinic', function ($value) {
    $tenant = app()->bound('tenant') ? app('tenant') : null;
    
    if (!$tenant) {
        abort(403, 'Tenant not identified');
    }
    
    return \App\Models\Clinic::where('id', $value)
        ->where('tenant_id', $tenant->id)
        ->firstOrFail();
});
```

✅ Route model binding correctly uses tenant from app container (set by IdentifyTenant)

---

## Routes Verified

### Tenant Routes (All use `['auth', 'tenant', 'tenant.access', 'subscription']`)

1. **Dashboard:**
   - `GET /dashboard` ✅

2. **Clinic Management:**
   - `GET /clinic` (index) ✅
   - `GET /clinic/create` ✅
   - `POST /clinic` (store) ✅
   - `GET /clinic/{clinic}` (show) ✅
   - `GET /clinic/{clinic}/edit` ✅ **Verified: Uses same middleware as index**
   - `PUT/PATCH /clinic/{clinic}` (update) ✅
   - `DELETE /clinic/{clinic}` (destroy) ✅

3. **Staff Management:**
   - `GET /staff` (index) ✅
   - `GET /staff/create` ✅
   - `POST /staff` (store) ✅
   - `GET /staff/{staff}` (show) ✅
   - `GET /staff/{staff}/edit` ✅ **Verified: Uses same middleware as index**
   - `PUT/PATCH /staff/{staff}` (update) ✅
   - `DELETE /staff/{staff}` (destroy) ✅
   - `POST /staff/{staff}/reset-password` ✅

4. **Queue Management:**
   - `GET /queues/{clinic}` ✅ **Uses route model binding**
   - `GET /api/queue/{clinic}` ✅
   - `POST /queues/{clinic}/next/{queueNumber}` ✅
   - `POST /queues/{clinic}/previous/{queueNumber}` ✅
   - `POST /queues/{clinic}/reset/{queueNumber}` ✅

5. **Service Management:**
   - `POST /services/{service}/verify` ✅
   - `GET /services/{service}` ✅
   - `GET /services/{service}/second-screen` ✅
   - `POST /services/{service}/broadcast` ✅

6. **Subscription:**
   - `GET /subscription` ✅
   - `GET /plans` ✅
   - `POST /plans/{plan:slug}/activate` ✅
   - `POST /plans/{plan:slug}/renew` ✅
   - `GET /metrics` ✅

### Platform Routes (NO tenant middleware)

- `GET /platform/dashboard` ✅ **Only uses `auth` middleware**
- `GET /tenant/select` ✅ **Only uses `auth` middleware**
- `POST /tenant/switch/{tenant:slug}` ✅ **Only uses `auth` middleware**
- `POST /tenant/exit` ✅ **Only uses `auth` middleware**

---

## Single Source of Truth

✅ **Tenant identification has ONE source of truth:**

**ONLY:** `auth()->user()->current_tenant_id`

**NO OTHER METHODS:**
- ❌ Subdomain checking
- ❌ Domain checking
- ❌ Route parameter checking
- ❌ Session-based resolution
- ❌ Request-based resolution

---

## How It Works

### For Tenant Routes

1. User makes request to `/clinic/{id}/edit`
2. `auth` middleware runs → User authenticated
3. `tenant` middleware (IdentifyTenant) runs:
   - Checks `auth()->user()->current_tenant_id`
   - Finds tenant from database
   - Verifies tenant is active
   - Registers tenant in app container: `app()->instance('tenant', $tenant)`
4. `tenant.access` middleware runs:
   - Verifies user has access to tenant
   - For Super Admin: Verifies `current_tenant_id` matches identified tenant
5. `subscription` middleware runs:
   - Checks subscription status
6. Route model binding resolves `{clinic}`:
   - Uses tenant from app container
   - Scopes clinic to tenant
7. Controller receives clinic model (already scoped to tenant)

### For Platform Routes

1. User makes request to `/platform/dashboard`
2. `auth` middleware runs → User authenticated
3. No `tenant` middleware → No tenant identification
4. Controller handles request without tenant context

---

## Security

✅ **Tenant Isolation Maintained:**
- Route model binding scopes resources to tenant
- Global TenantScope on models ensures data isolation
- Super Admin must explicitly enter tenant context
- Regular users can only access tenants they belong to

✅ **No Bypass:**
- Cannot infer tenant from route parameters
- Cannot use subdomain/domain to identify tenant
- Must have `current_tenant_id` set to access tenant routes

---

## Status

✅ **COMPLETE** - Tenant identification now uses `current_tenant_id` as the ONLY source of truth.

**Files Changed:**
1. `app/Http/Middleware/IdentifyTenant.php` - Rewritten to only use `current_tenant_id`
2. `bootstrap/app.php` - Removed global IdentifyTenant registration

**Files Verified (No Changes Needed):**
- ✅ All controllers - Only read tenant from app container
- ✅ All helpers - Only read tenant from app container
- ✅ Route groups - Correctly configured
- ✅ Route model binding - Correctly uses tenant from app container

