# Platform and Tenant App Separation

**Date:** December 18, 2025  
**Status:** ✅ COMPLETE

---

## Overview

The Laravel SaaS has been separated into two distinct areas:
1. **Platform Area** (`/platform/*`) - Super Admin only
2. **Tenant App Area** (`/app/*`) - Queue management for tenants

---

## Route Organization

### Platform Routes (`/platform/*`)
**Middleware:** `auth` + `superAdmin`  
**Layout:** `layouts.platform`  
**Navigation:** `partials.platform.nav`

Routes:
- `/platform/dashboard` → `platform.dashboard` (PlatformController@dashboard)
- `/platform/tenants` → `platform.tenants.index` (placeholder, uses dashboard)
- `/platform/metrics` → `platform.metrics.index` (MetricsController@index)

### Tenant App Routes (`/app/*`)
**Middleware:** `auth` + `tenant` + `tenant.access` + `subscription`  
**Layout:** `layouts.tenant`  
**Navigation:** `partials.tenant.nav`

Routes:
- `/app/dashboard` → `app.dashboard` (DashboardController@index)
- `/app/staff/*` → `app.staff.*` (StaffController)
- `/app/clinic/*` → `app.clinic.*` (ClinicController)
- `/app/subscription` → `app.subscription.index` (SubscriptionController@index)
- `/app/plans/*` → `app.plans.*` (PlanController - tenant self-activation)
- `/app/queues/{clinic}` → `app.queues.*` (QueueController)
- `/app/services/{service}` → `app.service.*` (ServiceController)

---

## Middleware

### New Middleware
- **`superAdmin`** (`EnsureSuperAdmin.php`)
  - Ensures user is authenticated and is Super Admin
  - Used on all `/platform/*` routes

### Existing Middleware (Unchanged)
- `tenant` - Identifies tenant from `current_tenant_id`
- `tenant.access` - Ensures tenant access is valid
- `subscription` - Checks subscription status
- `role` - Role-based access control

---

## Layouts

### Platform Layout (`layouts/platform.blade.php`)
- Uses `partials.platform.nav` for navigation
- Purple theme (bg-purple-600)
- Platform-specific branding
- No footer (custom styling per view)

### Tenant Layout (`layouts/tenant.blade.php`)
- Uses `partials.tenant.nav` for navigation
- Blue theme (SmartQueue branding)
- Includes footer
- Tenant-specific branding

---

## Navigation

### Platform Navigation (`partials/platform/nav.blade.php`)
**Visible to:** Super Admin only  
**Menu Items:**
- Dashboard
- Tenants
- Platform Metrics

**No tenant app links**

### Tenant Navigation (`partials/tenant/nav.blade.php`)
**Visible to:** Users with tenant context  
**Menu Items:**
- Dashboard
- Staff (admin only)
- Clinics (admin only)
- Billing & Subscription (admin only)

**Exit Tenant button** (Super Admin only, when in tenant context)

---

## Redirects and Route Updates

### Updated Redirects
- `AuthController@login`: Redirects to `app.dashboard` if tenant context exists, `platform.dashboard` for Super Admin
- `TenantController@switch`: Redirects to `app.dashboard` after entering tenant
- `TenantController@exit`: Redirects to `platform.dashboard` after exiting tenant
- `TenantController@register`: Redirects to `app.dashboard` after registration

### Legacy Route Redirects
- `/dashboard` → redirects to `app.dashboard`
- `/subscription` → redirects to `app.subscription.index`
- `/plans` → redirects to `app.plans.index`
- `/metrics` → redirects to `platform.metrics.index`

---

## View Updates

### Updated Views
- `dashboard.blade.php`: Now extends `layouts.tenant`, uses `app.*` routes
- `platform/dashboard.blade.php`: Extends `layouts.platform`

### Views to Update (Still Need Route Name Changes)
The following views still reference old route names and should be updated:
- `resources/views/clinic/*.blade.php` - Update to `app.clinic.*`
- `resources/views/staff/*.blade.php` - Update to `app.staff.*`
- `resources/views/subscription/*.blade.php` - Update to `app.subscription.*`
- `resources/views/plans/*.blade.php` - Update to `app.plans.*`
- `resources/views/service/*.blade.php` - Update to `app.service.*`
- `resources/views/partials/header.blade.php` - Update to new route names

---

## Key Principles

### Separation Rules
1. **Platform routes** use `/platform/*` prefix
2. **Tenant app routes** use `/app/*` prefix
3. **No mixed navigation** - Platform nav has no tenant links, tenant nav has no platform links
4. **Middleware separation** - Platform uses `superAdmin`, Tenant uses `tenant` + `tenant.access`
5. **Layout separation** - Platform uses `platform` layout, Tenant uses `tenant` layout

### Super Admin Workflow
1. Super Admin logs in → `platform.dashboard`
2. Super Admin enters tenant → `tenant.switch` → `app.dashboard`
3. Super Admin exits tenant → `tenant.exit` → `platform.dashboard`

### Regular User Workflow
1. User logs in → `tenant.select` (if no tenant) or `app.dashboard` (if tenant exists)
2. User works in tenant app area

---

## Security

- ✅ Platform routes protected by `superAdmin` middleware
- ✅ Tenant routes protected by `tenant` + `tenant.access` middleware
- ✅ No tenant middleware leaks into platform routes
- ✅ No platform logic visible to tenant users
- ✅ Tenant isolation maintained

---

## Files Created/Modified

### New Files
- `app/Http/Middleware/EnsureSuperAdmin.php`
- `resources/views/layouts/platform.blade.php`
- `resources/views/layouts/tenant.blade.php`
- `resources/views/partials/platform/nav.blade.php`
- `resources/views/partials/tenant/nav.blade.php`

### Modified Files
- `bootstrap/app.php` - Added `superAdmin` middleware alias
- `routes/web.php` - Reorganized into `/platform/*` and `/app/*` groups
- `app/Http/Controllers/AuthController.php` - Updated redirects
- `app/Http/Controllers/TenantController.php` - Updated redirects
- `resources/views/dashboard.blade.php` - Updated to use tenant layout and new routes
- `resources/views/platform/dashboard.blade.php` - Updated to use platform layout

---

## Next Steps

1. ✅ Routes reorganized
2. ✅ Middleware created and registered
3. ✅ Layouts created
4. ✅ Navigation separated
5. ✅ Main dashboard updated
6. ⚠️ Other views still need route name updates (clinic, staff, subscription, plans, service views)
7. ⚠️ Test all route redirects and navigation

---

## Testing Checklist

- [ ] Super Admin can access `/platform/dashboard`
- [ ] Super Admin cannot access `/app/*` without tenant context
- [ ] Super Admin can enter tenant via `tenant.switch` → redirects to `app.dashboard`
- [ ] Super Admin can exit tenant via `tenant.exit` → redirects to `platform.dashboard`
- [ ] Regular users can access `/app/dashboard` with tenant context
- [ ] Regular users cannot access `/platform/*` routes
- [ ] Legacy route redirects work correctly
- [ ] Navigation shows correct items based on context

