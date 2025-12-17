# Multi-Tenant SaaS Architecture Documentation

## Overview

This Laravel application has been refactored into a multi-tenant SaaS (Software as a Service) architecture, allowing multiple hospitals/organizations to use the queue management system independently with complete data isolation.

## Architecture Components

### 1. Database Schema

#### New Tables

**`tenants`**
- Stores organization/hospital information
- Fields: `id`, `name`, `slug`, `domain`, `email`, `phone`, `address`, `logo_path`, `settings` (JSON), `is_active`, `trial_ends_at`
- Supports soft deletes

**`subscriptions`**
- Manages subscription plans for tenants
- Fields: `id`, `tenant_id`, `plan_name`, `status`, `max_clinics`, `max_users`, `starts_at`, `ends_at`, `cancelled_at`, `features` (JSON)
- Statuses: `active`, `cancelled`, `expired`, `trial`

**`tenant_users`**
- Pivot table linking users to tenants
- Fields: `id`, `tenant_id`, `user_id`, `role`, `is_active`, `joined_at`
- Roles: `owner`, `admin`, `user`, `viewer`

#### Updated Tables

All existing tables now include `tenant_id`:
- `users` - Added `current_tenant_id` for tracking active tenant
- `clinics` - Added `tenant_id` foreign key
- `queues` - Added `tenant_id` foreign key
- `sub_queues` - Added `tenant_id` foreign key

### 2. Models & Relationships

#### Tenant Model (`app/Models/Tenant.php`)
- Relationships: `clinics()`, `queues()`, `subQueues()`, `users()`, `subscription()`, `subscriptions()`
- Methods: `hasActiveSubscription()`, `isOnTrial()`, `findBySlug()`, `findByDomain()`

#### Subscription Model (`app/Models/Subscription.php`)
- Relationships: `tenant()`
- Methods: `isActive()`, `isExpired()`, `cancel()`

#### Updated Models
- `User`: Added `currentTenant()`, `tenants()`, `belongsToTenant()`, `getRoleInTenant()`, `switchTenant()`
- `Clinic`, `Queue`, `SubQueue`: Added `tenant()` relationship and automatic tenant scoping

### 3. Middleware Strategy

#### IdentifyTenant Middleware
**Location:** `app/Http/Middleware/IdentifyTenant.php`

Identifies tenant from:
1. Subdomain (e.g., `hospital1.example.com`)
2. Custom domain (e.g., `hospital.example.com`)
3. Route parameter (`tenant` slug)
4. Authenticated user's current tenant

**Registered:** Globally in `bootstrap/app.php`

#### CheckSubscription Middleware
**Location:** `app/Http/Middleware/CheckSubscription.php`

- Validates tenant has active subscription or is on trial
- Sets subscription limits in request
- Redirects to subscription page if expired

**Usage:** Apply to routes requiring active subscription

#### EnsureUserBelongsToTenant Middleware
**Location:** `app/Http/Middleware/EnsureUserBelongsToTenant.php`

- Ensures authenticated user belongs to current tenant
- Automatically switches user's current tenant if needed

**Usage:** Apply to tenant-scoped routes

### 4. Tenant Scoping

#### Global Scope
**Location:** `app/Scopes/TenantScope.php`

Automatically filters queries to current tenant:
```php
// Automatically adds WHERE tenant_id = ? to queries
Clinic::all(); // Only returns clinics for current tenant
```

Applied to: `Clinic`, `Queue`, `SubQueue` models

#### Manual Scoping
For queries that need to bypass tenant scope:
```php
Clinic::withoutGlobalScope(TenantScope::class)->all();
```

### 5. Services

#### TenantService
**Location:** `app/Services/TenantService.php`

**Methods:**
- `createTenant()` - Create new tenant with owner and trial subscription
- `createSubscription()` - Create subscription for tenant
- `canCreateClinic()` - Check subscription limits
- `canAddUser()` - Check subscription limits

**Plan Configurations:**
- `trial`: 3 clinics, 2 users, 14 days
- `basic`: 10 clinics, 5 users
- `professional`: 50 clinics, 20 users
- `enterprise`: Unlimited

### 6. Controllers

#### New Controllers
- `TenantController` - Tenant registration, selection, switching
- `AuthController` - Login/logout with tenant awareness
- `SubscriptionController` - Subscription management

#### Updated Controllers
- `DashboardController` - Tenant-aware dashboard
- `QueueController` - All methods now tenant-scoped
- `OPDLabController` - Tenant-aware (if needed)

### 7. Routes Structure

```
Public Routes:
- / (home)
- /login, /register

Authenticated Routes (no tenant):
- /tenant/select

Tenant-Scoped Routes (require tenant + subscription):
- /dashboard
- /queues/*
- /opd-lab/*
- /api/*

Subscription Routes:
- /subscription
- /subscription/required
```

## Tenant Identification Methods

### Method 1: Subdomain
```
https://hospital1.example.com/dashboard
```
Tenant identified from subdomain `hospital1`

### Method 2: Custom Domain
```
https://hospital.example.com/dashboard
```
Tenant identified from custom domain

### Method 3: Route Parameter
```
https://example.com/hospital1/dashboard
```
Requires route parameter: `{tenant}`

### Method 4: User Session
For authenticated users, uses `current_tenant_id` from user record

## Subscription Management

### Trial Period
- New tenants get 14-day trial automatically
- Tracked via `tenants.trial_ends_at`
- No payment required during trial

### Subscription Plans
Plans are stored in `subscriptions` table with:
- `plan_name`: basic, professional, enterprise
- `max_clinics`: Maximum clinics allowed (-1 for unlimited)
- `max_users`: Maximum users allowed (-1 for unlimited)
- `features`: JSON array of enabled features

### Subscription Status
- `active`: Active subscription
- `trial`: On trial period
- `cancelled`: Cancelled by user
- `expired`: Subscription expired

## Security Features

### Data Isolation
- All queries automatically scoped to current tenant
- Foreign key constraints ensure data integrity
- Middleware prevents cross-tenant access

### User Access Control
- Users must belong to tenant to access data
- Role-based access (owner, admin, user, viewer)
- Session-based tenant switching

### Password Security
- Queue passwords still stored in plain text (legacy - consider hashing)
- User passwords properly hashed via Laravel

## Best Practices Implemented

### 1. Row-Level Security
- Every tenant-scoped model includes `tenant_id`
- Global scope ensures automatic filtering
- Foreign keys prevent orphaned records

### 2. Tenant Isolation
- No shared data between tenants
- Each tenant has isolated database records
- Middleware enforces tenant boundaries

### 3. Scalability
- Supports unlimited tenants
- Subscription limits prevent resource abuse
- Efficient query scoping

### 4. Multi-Tenancy Patterns
- **Shared Database, Shared Schema**: All tenants in same database
- **Row-Level Isolation**: `tenant_id` on every table
- **Automatic Scoping**: Global scope handles filtering

## Migration Guide

### Running Migrations
```bash
php artisan migrate
```

This will:
1. Create `tenants`, `subscriptions`, `tenant_users` tables
2. Add `tenant_id` to existing tables
3. Add `current_tenant_id` to users table

### Data Migration
For existing data, you'll need to:
1. Create a default tenant
2. Assign all existing records to that tenant
3. Create initial subscription

Example migration script:
```php
$tenant = Tenant::create([...]);
Clinic::query()->update(['tenant_id' => $tenant->id]);
Queue::query()->update(['tenant_id' => $tenant->id]);
// etc.
```

## API Integration Points

### Billing Integration
The subscription system is designed to integrate with:
- Stripe
- PayPal
- Custom payment gateways

Integration points:
- `SubscriptionController` - Handle webhooks
- `TenantService::createSubscription()` - Create paid subscriptions
- `Subscription::cancel()` - Handle cancellations

### Webhook Endpoints (To Be Implemented)
```
POST /webhooks/stripe
POST /webhooks/paypal
```

## Testing Considerations

### Tenant Isolation Testing
```php
// Test that tenants can't access each other's data
$tenant1 = Tenant::factory()->create();
$tenant2 = Tenant::factory()->create();

app()->instance('tenant', $tenant1);
$clinics = Clinic::all(); // Should only return tenant1's clinics
```

### Subscription Testing
```php
// Test subscription limits
$tenant = Tenant::factory()->create();
$subscription = Subscription::factory()->create([
    'tenant_id' => $tenant->id,
    'max_clinics' => 5
]);

// Should fail after 5 clinics
```

## Future Enhancements

1. **Database Per Tenant** (optional)
   - For maximum isolation
   - Requires dynamic database switching

2. **Custom Domains**
   - Full support for custom domains
   - DNS configuration required

3. **Advanced Billing**
   - Usage-based billing
   - Overage charges
   - Discount codes

4. **Tenant Analytics**
   - Per-tenant usage metrics
   - Billing reports
   - Performance monitoring

5. **API Access**
   - Tenant-specific API keys
   - Rate limiting per tenant
   - Webhook support

## Troubleshooting

### Tenant Not Identified
- Check middleware is registered
- Verify subdomain/domain configuration
- Check user's `current_tenant_id`

### Subscription Errors
- Verify subscription exists and is active
- Check trial expiration
- Review subscription status

### Data Isolation Issues
- Ensure global scope is applied
- Check `tenant_id` is set on all records
- Verify foreign key constraints

## Support

For issues or questions about the SaaS architecture, refer to:
- Laravel Multi-Tenancy documentation
- Tenant isolation best practices
- Subscription management patterns

