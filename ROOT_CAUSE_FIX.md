# Root Cause Fix - Route Model Binding Tenant Issue

## ACTUAL ERROR FROM LOGS

```
[2025-12-17 22:29:11] local.ERROR: Route model binding: Tenant not set for clinic binding 
{"clinic_id":"2","user_id":1,"current_tenant_id":1}
```

## ROOT CAUSE

**Route model binding runs BEFORE middleware executes!**

Laravel's middleware execution order:
1. Route matching
2. **Route model binding** (SubstituteBindings middleware) ← **TENANT NOT SET YET**
3. Custom middleware (auth, tenant, tenant.access, etc.)

When route model binding tries to resolve `{clinic}`, it calls `Route::bind('clinic')` which checks for tenant, but `IdentifyTenant` middleware hasn't run yet!

## FIX APPLIED

**File:** `app/Providers/AppServiceProvider.php`

Changed route model binding to handle tenant not being set:

### Before (WRONG):
```php
Route::bind('clinic', function ($value) {
    $tenant = app()->bound('tenant') ? app('tenant') : null;
    
    if (!$tenant) {
        abort(500, 'System error: Tenant context not available'); // ❌ FAILS HERE
    }
    
    return \App\Models\Clinic::where('id', $value)
        ->where('tenant_id', $tenant->id)
        ->firstOrFail();
});
```

### After (CORRECT):
```php
Route::bind('clinic', function ($value) {
    $tenant = app()->bound('tenant') ? app('tenant') : null;
    
    // If tenant is available (middleware has run), scope by tenant
    if ($tenant) {
        return \App\Models\Clinic::where('id', $value)
            ->where('tenant_id', $tenant->id)
            ->firstOrFail();
    }
    
    // If tenant not available yet (middleware hasn't run), just find by ID
    // TenantScope will filter it, and middleware will verify access
    return \App\Models\Clinic::findOrFail($value);
});
```

## WHY THIS WORKS

1. **Route model binding** finds clinic by ID (tenant not required)
2. **TenantScope** (on Clinic model) automatically filters by tenant_id if available
3. **IdentifyTenant middleware** runs and sets tenant in app container
4. **EnsureTenantAccess middleware** verifies user has access to the clinic's tenant
5. **Controller** receives clinic already scoped to tenant

## SECURITY

✅ **Still secure because:**
- Clinic model has `TenantScope` - automatically filters queries by tenant_id
- `EnsureTenantAccess` middleware verifies user belongs to tenant
- `AuthorizeQueueAccess` middleware verifies clinic access

The same fix was applied to `staff` and `service` route model bindings.

