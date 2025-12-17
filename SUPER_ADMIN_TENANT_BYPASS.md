# Super Admin Tenant Context Bypass

## Overview

Super Admin users now operate **outside tenant context**. They are platform administrators who manage the entire system without being restricted by tenant-level plans, subscriptions, or permissions.

## Changes Made

### 1. Middleware Updates

#### IdentifyTenant Middleware
- **Change**: Skips tenant identification for Super Admin users
- **Result**: Super Admin users don't have tenant context set, allowing them to access all tenants' data

#### EnsureTenantAccess Middleware
- **Change**: Allows Super Admin to proceed without tenant context
- **Result**: Super Admin bypasses tenant access checks

#### EnsureUserBelongsToTenant Middleware
- **Change**: Skips tenant membership verification for Super Admin
- **Result**: Super Admin doesn't need to belong to a tenant

### 2. Route Model Bindings (AppServiceProvider)

#### Clinic Binding
- **Change**: Super Admin can access any clinic without tenant scoping
- **Implementation**: Uses `withoutGlobalScopes()` to bypass TenantScope

#### Staff Binding
- **Change**: Super Admin can access any user/staff without tenant scoping
- **Implementation**: Direct User lookup without tenant verification

#### Service Binding
- **Change**: Super Admin can access any service without tenant scoping
- **Implementation**: Uses `withoutGlobalScopes()` to bypass TenantScope

### 3. User Model Methods

#### getCurrentRole()
- **Change**: Returns 'admin' for Super Admin (for UI compatibility)
- **Result**: UI treats Super Admin as admin role

#### hasRole()
- **Change**: Always returns `true` for Super Admin
- **Result**: Super Admin has all roles

#### isAdmin()
- **Change**: Always returns `true` for Super Admin
- **Result**: All admin checks pass for Super Admin

#### canManageQueues()
- **Change**: Always returns `true` for Super Admin
- **Result**: Super Admin can manage all queues

#### canAccessLab()
- **Change**: Always returns `true` for Super Admin
- **Result**: Super Admin can access all services

### 4. SubscriptionHelper Updates

#### getCurrentPlan()
- **Change**: Returns `null` for Super Admin (no plan restrictions)
- **Result**: UI won't show plan-based restrictions

#### getMaxScreens()
- **Change**: Returns `-1` (unlimited) for Super Admin
- **Result**: Super Admin can use unlimited screens

#### All limit checks (canCreateClinic, canAddUser, canOpenScreen, hasFeature)
- **Change**: All return `true` or appropriate unlimited values for Super Admin
- **Result**: All subscription limits bypassed

## Behavior

### Super Admin Access
- ✅ Can access any tenant's data
- ✅ Can create unlimited clinics, staff, and services
- ✅ Can use unlimited display screens
- ✅ Has access to all features
- ✅ No plan restrictions
- ✅ No subscription checks

### Tenant Admin Access (Unchanged)
- ❌ Restricted by subscription plans
- ❌ Limited by plan features
- ❌ Subject to clinic/staff/screen limits
- ✅ Must belong to tenant
- ✅ Must have tenant context

## Security

### What Super Admin CAN Do
- Access all tenants' data
- Create/modify/delete any resource across tenants
- Bypass all subscription and plan limits

### What Super Admin CANNOT Do
- This bypass does NOT weaken tenant security
- Tenant data is still isolated (Super Admin uses `withoutGlobalScopes()` explicitly)
- Regular users still require tenant membership
- Tenant admins are still restricted by plans

## Testing

To verify Super Admin bypass works:

1. **Set a user as Super Admin**:
   ```php
   $user = User::find(1);
   $user->is_super_admin = true;
   $user->save();
   ```

2. **Login as Super Admin**

3. **Verify**:
   - Can access dashboard without tenant context
   - No upgrade messages shown
   - All buttons enabled (Add Clinic, Add Staff, etc.)
   - Can create unlimited resources
   - Can access any tenant's clinics/services

## Notes

- Super Admin's `current_tenant_id` may still be set, but it's ignored
- Tenant context is explicitly skipped for Super Admin in middleware
- Route model bindings bypass tenant scoping for Super Admin
- UI checks use `auth()->user()->isSuperAdmin()` to hide restrictions

