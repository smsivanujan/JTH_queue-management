# Super Admin Implementation

## Overview

Super Admin (Platform Administrator) functionality has been implemented to allow platform admins to bypass all subscription plan restrictions while maintaining tenant security and role-based access control.

## Key Points

1. **Super Admin Definition**: User with `is_super_admin = true` in the `users` table
2. **Bypass Scope**: Only subscription plan/feature limits are bypassed
3. **Security Maintained**: Tenant isolation and role-based access control remain enforced
4. **Tenant Admins Restricted**: Regular tenant admins still respect subscription plans

## Database Changes

### Migration: `add_is_super_admin_to_users_table`

Added `is_super_admin` boolean field to `users` table (defaults to `false`).

```sql
ALTER TABLE users ADD COLUMN is_super_admin BOOLEAN DEFAULT false;
```

## User Model Changes

### Method: `isSuperAdmin()`

```php
public function isSuperAdmin(): bool
{
    return $this->is_super_admin === true;
}
```

## Middleware Updates

### 1. CheckSubscription Middleware

**File**: `app/Http/Middleware/CheckSubscription.php`

**Change**: Added super admin bypass at the beginning of `handle()` method.

```php
// Super Admin bypass: Platform admins are not restricted by subscription plans
if (auth()->check() && auth()->user()->isSuperAdmin()) {
    return $next($request);
}
```

**Effect**: Super admins bypass subscription expiry checks and can access all routes.

### 2. EnforceClinicLimit Middleware

**File**: `app/Http/Middleware/EnforceClinicLimit.php`

**Change**: Added super admin bypass before limit check.

```php
// Super Admin bypass: Platform admins can create unlimited clinics
if (auth()->check() && auth()->user()->isSuperAdmin()) {
    return $next($request);
}
```

**Effect**: Super admins can create unlimited clinics regardless of plan limits.

### 3. EnforceScreenLimit Middleware

**File**: `app/Http/Middleware/EnforceScreenLimit.php`

**Change**: Added super admin bypass before limit check.

```php
// Super Admin bypass: Platform admins can use unlimited screens
if (auth()->check() && auth()->user()->isSuperAdmin()) {
    return $next($request);
}
```

**Effect**: Super admins can use unlimited display screens regardless of plan limits.

### 4. CheckPlanFeature Middleware

**File**: `app/Http/Middleware/CheckPlanFeature.php`

**Change**: Added super admin bypass before feature check.

```php
// Super Admin bypass: Platform admins have access to all features
if (auth()->check() && auth()->user()->isSuperAdmin()) {
    return $next($request);
}
```

**Effect**: Super admins have access to all plan features (e.g., OPD Lab, services).

## Helper Updates

### SubscriptionHelper

**File**: `app/Helpers/SubscriptionHelper.php`

#### Methods Updated:

1. **`hasFeature($feature)`**
   - Returns `true` for super admins (all features enabled)

2. **`canCreateClinic()`**
   - Returns `true` for super admins (unlimited clinics)

3. **`canAddUser()`**
   - Returns `true` for super admins (unlimited staff)

4. **`canOpenScreen()`**
   - Returns `true` for super admins (unlimited screens)

**Pattern Used**:
```php
// Super Admin bypass: Platform admins can [action]
if (auth()->check() && auth()->user()->isSuperAdmin()) {
    return true;
}
```

## Blade View Updates

### Blade Directive Added

**File**: `app/Providers/BladeServiceProvider.php`

Added `@superAdmin` directive:

```php
Blade::if('superAdmin', function () {
    if (!auth()->check()) {
        return false;
    }
    return auth()->user()->isSuperAdmin();
});
```

### Views Updated

1. **dashboard.blade.php**
   - Hide upgrade messages for super admin
   - Enable clinic creation buttons for super admin

2. **clinic/index.blade.php**
   - Hide "Add Clinic" disabled state and tooltip for super admin
   - Always show enabled "Add Clinic" button for super admin
   - Hide clinic limit warning banner for super admin

3. **staff/index.blade.php**
   - Hide "Add Staff" disabled state and tooltip for super admin
   - Always show enabled "Add Staff" button for super admin
   - Hide staff limit warning banner for super admin

**Pattern Used**:
```blade
@unless(auth()->check() && auth()->user()->isSuperAdmin())
    {{-- Show upgrade message/disabled button --}}
@else
    {{-- Show enabled button for super admin --}}
@endunless
```

## Controller Updates

Controllers that use `SubscriptionHelper` methods automatically benefit from super admin bypass:

- `ClinicController::create()` - Uses `SubscriptionHelper::canCreateClinic()`
- `ClinicController::store()` - Uses `SubscriptionHelper::canCreateClinic()`
- `StaffController::create()` - Uses `SubscriptionHelper::canAddUser()`
- `StaffController::store()` - Uses `SubscriptionHelper::canAddUser()`

No direct controller changes needed - they inherit the bypass from helper methods.

## What Super Admin Can Do

✅ **Bypass Subscription Checks**
- Access routes without active subscription
- No subscription expiry redirects

✅ **Unlimited Resources**
- Create unlimited clinics
- Add unlimited staff members
- Use unlimited display screens

✅ **All Features Enabled**
- Access all plan features (OPD Lab, services, etc.)
- No feature restrictions

❌ **Still Restricted By**
- Tenant isolation (must belong to tenant or have access)
- Role-based access control (RBAC) within tenants
- Tenant-level permissions (still respects tenant admin roles)

## What Tenant Admins Cannot Do

❌ **Still Restricted By**
- Subscription plan limits (clinics, staff, screens)
- Feature availability (must be on plan that includes feature)
- Subscription expiry (must have active subscription)

## Setting Up a Super Admin

### Method 1: Via Database

```sql
UPDATE users SET is_super_admin = true WHERE email = 'admin@example.com';
```

### Method 2: Via Tinker

```php
php artisan tinker
$user = User::where('email', 'admin@example.com')->first();
$user->is_super_admin = true;
$user->save();
```

### Method 3: Via Seeder (for initial setup)

```php
User::where('email', 'admin@example.com')
    ->update(['is_super_admin' => true]);
```

## Testing Checklist

- [x] Super admin bypasses subscription check middleware
- [x] Super admin can create unlimited clinics
- [x] Super admin can add unlimited staff
- [x] Super admin can use unlimited screens
- [x] Super admin has access to all features
- [x] Upgrade messages hidden for super admin
- [x] Buttons enabled for super admin
- [x] Tenant admin still restricted by plans
- [x] Tenant isolation maintained
- [x] RBAC maintained within tenants

## Security Notes

1. **Tenant Isolation**: Super admin bypass does NOT affect tenant isolation. Users must still belong to tenants or have explicit access.

2. **Role-Based Access**: Super admin bypass does NOT affect RBAC. A super admin still needs appropriate tenant role to perform tenant-specific actions.

3. **Scope**: Super admin bypass applies ONLY to subscription plan restrictions, not to security checks.

4. **Audit Trail**: Consider logging super admin actions separately for audit purposes.

## Files Modified

### Models
- `app/Models/User.php` - Added `isSuperAdmin()` method

### Middleware
- `app/Http/Middleware/CheckSubscription.php`
- `app/Http/Middleware/EnforceClinicLimit.php`
- `app/Http/Middleware/EnforceScreenLimit.php`
- `app/Http/Middleware/CheckPlanFeature.php`

### Helpers
- `app/Helpers/SubscriptionHelper.php`

### Providers
- `app/Providers/BladeServiceProvider.php` - Added `@superAdmin` directive

### Views
- `resources/views/dashboard.blade.php`
- `resources/views/clinic/index.blade.php`
- `resources/views/staff/index.blade.php`

### Migrations
- `database/migrations/2025_12_17_204706_add_is_super_admin_to_users_table.php`

