# Super Admin Tenant Context Management

## Overview

Super Admin users now **explicitly enter tenant context** rather than bypassing it. This provides better security, clarity, and maintains proper tenant isolation.

## How It Works

### 1. Platform Level (No Tenant Context)

When Super Admin logs in without a tenant:
- ✅ Can see list of ALL tenants (not just ones they belong to)
- ✅ Can access platform-level routes (tenant selection)
- ✅ Cannot access tenant-scoped routes (dashboard, clinics, etc.)

### 2. Entering Tenant Context

Super Admin can explicitly enter a tenant context:
1. Visit `/tenant/select` - sees all tenants
2. Click "Enter" or switch to a tenant
3. System sets `current_tenant_id` on the user
4. Redirected to tenant dashboard
5. Now operates **within that tenant's context**

### 3. Operating in Tenant Context

Once Super Admin enters a tenant context:
- ✅ Can access all tenant-scoped routes normally
- ✅ Tenant middleware allows access (no membership check for Super Admin)
- ✅ All subscription/plan limits bypassed (existing Super Admin bypass)
- ✅ Can manage tenant's resources (clinics, staff, services, etc.)
- ✅ Data is scoped to that tenant (proper isolation maintained)

### 4. Exiting Tenant Context

Super Admin can exit tenant context:
- POST to `/tenant/exit`
- Sets `current_tenant_id` to `null`
- Returns to platform level (tenant selection page)

## Changes Made

### Middleware Updates

#### IdentifyTenant
- **Before**: Skipped tenant identification for Super Admin
- **After**: Allows Super Admin with `current_tenant_id` set to use tenant context
- **Result**: Super Admin can explicitly enter tenant context

#### EnsureTenantAccess
- **Before**: Completely bypassed for Super Admin
- **After**: Allows Super Admin without tenant to access platform routes, requires tenant for tenant-scoped routes
- **Result**: Super Admin must explicitly enter tenant to access tenant routes

#### EnsureUserBelongsToTenant
- **Before**: Completely bypassed for Super Admin
- **After**: Allows Super Admin to enter any tenant (no membership check)
- **Result**: Super Admin can switch to any tenant context

### Controller Updates

#### TenantController
- `select()`: Shows ALL tenants for Super Admin, only user's tenants for regular users
- `switch()`: Allows Super Admin to enter any tenant, regular users must belong
- `exit()`: New method to exit tenant context (Super Admin only)

### Model Updates

#### User Model
- `switchTenant()`: Updated to allow Super Admin to switch to any tenant
- `exitTenantContext()`: New method to exit tenant context

### Route Updates

- Added `POST /tenant/exit` route for Super Admin to exit tenant context
- Added route to `isPublicRoute` check in IdentifyTenant

### Route Model Bindings

- **Before**: Super Admin could access resources without tenant scoping
- **After**: Super Admin accesses resources within their current tenant context
- **Result**: Proper tenant isolation maintained, but Super Admin can enter any tenant

## Workflow

### Super Admin Workflow

```
1. Login → No tenant context
   ↓
2. Visit /tenant/select → See all tenants
   ↓
3. Click "Enter" on a tenant → Sets current_tenant_id
   ↓
4. Redirected to /dashboard → Operate in tenant context
   ↓
5. Can access all tenant routes normally
   ↓
6. POST /tenant/exit → Clear current_tenant_id
   ↓
7. Back to /tenant/select → Platform level
```

### Regular User Workflow (Unchanged)

```
1. Login → No tenant context (or has current_tenant_id)
   ↓
2. Visit /tenant/select → See only their tenants
   ↓
3. Click "Switch" on a tenant → Sets current_tenant_id (if belongs to tenant)
   ↓
4. Redirected to /dashboard → Operate in tenant context
```

## Security

### Tenant Isolation Maintained
- ✅ Super Admin must explicitly enter tenant context
- ✅ Data is scoped to the tenant Super Admin has entered
- ✅ Route model bindings still enforce tenant scoping
- ✅ No cross-tenant data leaks

### Super Admin Privileges
- ✅ Can enter ANY tenant (no membership check)
- ✅ Bypasses all subscription/plan limits (existing bypass)
- ✅ Has all roles/permissions (existing bypass)
- ✅ Can exit tenant context at any time

### Regular User Security (Unchanged)
- ✅ Must belong to tenant to enter it
- ✅ Subject to subscription/plan limits
- ✅ Role-based access control applies

## API Endpoints

### Tenant Selection
- `GET /tenant/select` - Show tenant selection page
- `POST /tenant/switch/{tenant:slug}` - Enter/switch tenant context
- `POST /tenant/exit` - Exit tenant context (Super Admin only)

## UI Considerations

When Super Admin is in tenant context:
- Dashboard shows tenant's clinics
- All tenant-scoped features work normally
- Can add "Exit Tenant Context" button in navigation

When Super Admin is at platform level:
- Tenant selection page shows all tenants
- Cannot access tenant-scoped routes (redirects to tenant selection)
- Can manage platform-level features (metrics, etc.)

## Benefits

1. **Security**: Tenant isolation maintained - Super Admin must explicitly enter context
2. **Clarity**: Clear separation between platform level and tenant level
3. **Flexibility**: Super Admin can switch between tenants easily
4. **Consistency**: Same route structure for both Super Admin and regular users
5. **Auditability**: Clear which tenant context Super Admin is operating in

