# Quick Start Guide - Multi-Tenant SaaS Queue Management

## Prerequisites

- PHP 8.2+
- Laravel 12
- MySQL/PostgreSQL
- Composer

## Installation Steps

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Configure Environment

Copy `.env.example` to `.env` and configure:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=queue_management
DB_USERNAME=root
DB_PASSWORD=

# Tenant Configuration
TENANT_SUBDOMAIN_ENABLED=true
```

### 3. Run Migrations

```bash
php artisan migrate
```

This creates:
- Tenant tables
- Subscription tables
- Adds `tenant_id` to existing tables

### 4. Create Default Tenant (For Existing Data)

If you have existing data, create a default tenant:

```bash
php artisan tinker
```

```php
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Support\Facades\Hash;

$tenantService = app(TenantService::class);

// Create admin user
$user = User::firstOrCreate(
    ['email' => 'admin@example.com'],
    [
        'name' => 'Admin',
        'password' => Hash::make('password')
    ]
);

// Create tenant
$tenant = $tenantService->createTenant([
    'name' => 'Default Hospital',
    'email' => 'admin@example.com',
], $user, 'professional');

// Assign existing data to tenant
\DB::table('clinics')->update(['tenant_id' => $tenant->id]);
\DB::table('queues')->update(['tenant_id' => $tenant->id]);
\DB::table('sub_queues')->update(['tenant_id' => $tenant->id]);
```

### 5. Start Development Server

```bash
php artisan serve
```

Or use the dev script:

```bash
composer run dev
```

## Usage

### Register New Tenant

1. Visit `/register`
2. Fill in organization and user details
3. Tenant is created with 14-day trial
4. User is set as tenant owner

### Login

1. Visit `/login`
2. Enter credentials
3. If user has `current_tenant_id`, redirects to dashboard
4. Otherwise, shows tenant selection

### Access Dashboard

- Requires authentication
- Requires tenant identification
- Requires active subscription or trial

### Create Clinic

```php
use App\Models\Clinic;
use App\Helpers\TenantHelper;

$tenant = TenantHelper::current();

$clinic = Clinic::create([
    'name' => 'Cardiology',
    'tenant_id' => $tenant->id,
]);
```

### Manage Queue

All queue operations are automatically scoped to current tenant:

```php
// Only returns queues for current tenant
$queues = Queue::all();

// Automatically sets tenant_id
$queue = Queue::create([
    'clinic_id' => 1,
    'display' => 3,
]);
```

## Tenant Identification

The system identifies tenants via:

1. **Subdomain** (recommended for production)
   - `hospital1.example.com` → Tenant with slug `hospital1`

2. **Custom Domain**
   - `hospital.example.com` → Tenant with domain `hospital.example.com`

3. **Route Parameter**
   - `/hospital1/dashboard` → Tenant with slug `hospital1`

4. **User Session**
   - Authenticated user's `current_tenant_id`

## Subscription Management

### Check Subscription

```php
$tenant = TenantHelper::current();

if ($tenant->hasActiveSubscription()) {
    // Has active subscription
}

if ($tenant->isOnTrial()) {
    // On trial period
}
```

### Create Subscription

```php
use App\Services\TenantService;

$tenantService = app(TenantService::class);
$subscription = $tenantService->createSubscription($tenant, 'professional');
```

### Check Limits

```php
$tenantService = app(TenantService::class);

if ($tenantService->canCreateClinic($tenant)) {
    // Can create more clinics
}

if ($tenantService->canAddUser($tenant)) {
    // Can add more users
}
```

## API Usage

### Get Live Queue

```http
GET /api/queue/{clinicId}
```

Automatically scoped to current tenant.

### Queue Operations

```http
POST /queues/{clinicId}/next/{queueNumber}
POST /queues/{clinicId}/previous/{queueNumber}
POST /queues/{clinicId}/reset/{queueNumber}
```

All operations are tenant-scoped.

## Common Tasks

### Switch Tenant

```php
$user = auth()->user();
$tenant = Tenant::find(2);

if ($user->belongsToTenant($tenant->id)) {
    $user->switchTenant($tenant);
}
```

### Add User to Tenant

```php
$tenant->users()->attach($userId, [
    'role' => 'admin',
    'is_active' => true,
    'joined_at' => now(),
]);
```

### Get User's Role

```php
$role = $user->getRoleInTenant($tenantId);
// Returns: 'owner', 'admin', 'user', or 'viewer'
```

## Testing

### Test Tenant Isolation

```php
$tenant1 = Tenant::create(['name' => 'Hospital 1', 'slug' => 'h1', ...]);
$tenant2 = Tenant::create(['name' => 'Hospital 2', 'slug' => 'h2', ...]);

// Set tenant 1
TenantHelper::setTenant($tenant1);
$clinic1 = Clinic::create(['name' => 'Clinic 1', 'tenant_id' => $tenant1->id]);

// Switch to tenant 2
TenantHelper::setTenant($tenant2);
$clinics = Clinic::all(); // Should not include clinic1
```

## Troubleshooting

### "Tenant not found"

- Check middleware is registered in `bootstrap/app.php`
- Verify tenant exists in database
- Check subdomain/domain configuration

### "Subscription expired"

- Create subscription: `$tenantService->createSubscription($tenant, 'basic')`
- Or extend trial: `$tenant->update(['trial_ends_at' => now()->addDays(14)])`

### "User can't access tenant"

- Add user to tenant: `$tenant->users()->attach($userId, ['role' => 'user'])`
- Check `is_active` in `tenant_users` table

### Data visible across tenants

- Verify global scope is applied: Check model `booted()` method
- Ensure `tenant_id` is set on all records
- Check foreign key constraints

## Production Deployment

### 1. Configure Subdomains

**Nginx:**
```nginx
server {
    server_name *.example.com;
    root /path/to/public;
    # ... rest of config
}
```

**DNS:**
```
*.example.com A 1.2.3.4
```

### 2. Update Environment

```env
APP_URL=https://example.com
SESSION_DOMAIN=.example.com
```

### 3. Set Up SSL

Use Let's Encrypt with wildcard certificate:
```bash
certbot certonly --dns-cloudflare -d *.example.com
```

### 4. Run Migrations

```bash
php artisan migrate --force
```

### 5. Create Initial Tenant

Use seeder or tinker to create first tenant.

## Support

For detailed documentation:
- `SAAS_ARCHITECTURE.md` - Architecture details
- `MIGRATION_GUIDE.md` - Migration instructions
- `REFACTORING_SUMMARY.md` - Refactoring overview

