# Super Admin Tenant Switch UI Implementation

## Overview

This implementation provides a comprehensive Super Admin platform dashboard with tenant management capabilities. Super Admin can view all tenants, see their subscription status, and explicitly enter/exit tenant contexts.

## Components

### 1. Platform Dashboard (`/platform/dashboard`)

**Controller**: `PlatformController@dashboard`

**Features**:
- Lists ALL tenants (not just ones Super Admin belongs to)
- Shows tenant statistics:
  - Total tenants
  - Active tenants
  - Inactive tenants
  - Tenants with active subscriptions
  - Tenants on trial
- Displays tenant details:
  - Name and email
  - Status (Active, Inactive, Trial, Expired, No Subscription)
  - Current plan name
  - Subscription expiration date
  - "Enter" button to enter tenant context

**Route**: `GET /platform/dashboard` (requires auth, no tenant context)

### 2. Tenant Entry/Exit

**Entry**:
- From platform dashboard: Click "Enter" button on any tenant
- Sets `current_tenant_id` on Super Admin user
- Redirects to tenant dashboard
- Super Admin now operates in that tenant's context

**Exit**:
- "Exit Tenant" button in tenant dashboard navigation
- POST to `/tenant/exit`
- Clears `current_tenant_id`
- Returns to platform dashboard

### 3. Navigation Updates

**When Super Admin is at Platform Level**:
- Platform Dashboard link available
- Cannot access tenant-scoped routes (redirects to platform dashboard)

**When Super Admin is in Tenant Context**:
- "Platform" link in navigation to return to platform dashboard
- "Exit Tenant" button in header
- Can access all tenant routes normally
- Sees tenant's clinics, staff, services, etc.

### 4. Route Protection

**Platform Routes** (No tenant required):
- `GET /platform/dashboard` - Platform dashboard
- `GET /tenant/select` - Tenant selection
- `POST /tenant/switch/{tenant:slug}` - Enter tenant
- `POST /tenant/exit` - Exit tenant context

**Tenant Routes** (Requires tenant context):
- All routes under `tenant` middleware group
- Super Admin must have `current_tenant_id` set
- Properly scoped to the tenant they've entered

## Workflow

### Super Admin Login Flow

```
1. Super Admin logs in
   ↓
2. Check: Has current_tenant_id?
   ├─ YES → Redirect to /dashboard (tenant context)
   └─ NO → Redirect to /platform/dashboard (platform level)
```

### Entering Tenant Context

```
1. Super Admin at /platform/dashboard
   ↓
2. Click "Enter" on a tenant
   ↓
3. POST /tenant/switch/{tenant:slug}
   ↓
4. Sets current_tenant_id = tenant->id
   ↓
5. Redirect to /dashboard
   ↓
6. Now operating in tenant context
```

### Exiting Tenant Context

```
1. Super Admin at /dashboard (in tenant context)
   ↓
2. Click "Exit Tenant" button
   ↓
3. POST /tenant/exit
   ↓
4. Sets current_tenant_id = null
   ↓
5. Redirect to /platform/dashboard
   ↓
6. Back to platform level
```

## UI Features

### Platform Dashboard

- **Statistics Cards**: Overview of tenant statuses
- **Tenants Table**:
  - Responsive design (mobile-friendly)
  - Status badges with color coding:
    - Green: Active
    - Red: Inactive
    - Yellow: Trial
    - Gray: Expired
    - Blue: No Subscription
  - Plan information
  - Subscription expiration dates
  - Quick "Enter" action button

### Tenant Dashboard (When in Context)

- Shows "Platform" link in navigation
- Shows "Exit Tenant" button in header
- All tenant features work normally
- Subscription bypass still applies (Super Admin privilege)

## Security

### Tenant Isolation

- Super Admin must explicitly enter tenant context
- Data is scoped to the tenant they've entered
- No cross-tenant data access
- Route model bindings enforce tenant scoping

### Access Control

- Platform dashboard: Super Admin only
- Tenant entry: Super Admin can enter ANY tenant
- Tenant exit: Super Admin only
- Tenant routes: Still require tenant context

### Middleware Flow

1. `IdentifyTenant`: Identifies tenant from `current_tenant_id` if set
2. `EnsureTenantAccess`: Allows platform routes for Super Admin, requires tenant for tenant routes
3. `EnsureUserBelongsToTenant`: Skips membership check for Super Admin
4. All subscription/limit middleware: Still bypasses for Super Admin

## Files Created/Modified

### New Files

- `app/Http/Controllers/PlatformController.php` - Platform dashboard controller
- `resources/views/platform/dashboard.blade.php` - Platform dashboard view

### Modified Files

- `routes/web.php` - Added platform dashboard route
- `app/Http/Middleware/EnsureTenantAccess.php` - Added platform.dashboard to allowed routes
- `app/Http/Middleware/IdentifyTenant.php` - Added platform.dashboard to public routes
- `app/Http/Controllers/DashboardController.php` - Redirects Super Admin to platform dashboard if no tenant
- `app/Http/Controllers/AuthController.php` - Redirects Super Admin to platform dashboard on login
- `resources/views/dashboard.blade.php` - Added "Platform" link and "Exit Tenant" button
- `resources/views/tenant/select.blade.php` - Added back link to platform dashboard

## Benefits

1. **Clear Separation**: Platform level vs tenant level is explicit
2. **Better UX**: Super Admin can see all tenants at a glance
3. **Easy Navigation**: Quick access to enter/exit tenant contexts
4. **Security**: Tenant isolation maintained through explicit context entry
5. **Auditability**: Clear which tenant context Super Admin is operating in
6. **Scalability**: Easy to add more platform-level features in the future

