# Migration Guide: Single-Tenant to Multi-Tenant SaaS

This guide will help you migrate your existing single-tenant queue management system to the new multi-tenant SaaS architecture.

## Pre-Migration Checklist

- [ ] Backup your database
- [ ] Review existing data structure
- [ ] Test migrations on staging environment
- [ ] Plan tenant creation strategy
- [ ] Prepare user migration data

## Step 1: Run Database Migrations

```bash
php artisan migrate
```

This will create:
- `tenants` table
- `subscriptions` table
- `tenant_users` table
- Add `tenant_id` columns to existing tables
- Add `current_tenant_id` to `users` table

## Step 2: Create Default Tenant

For existing installations, you'll need to create a default tenant for your existing data.

### Option A: Using Tinker

```bash
php artisan tinker
```

```php
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantService;

$tenantService = app(\App\Services\TenantService::class);

// Get or create a default user
$user = User::firstOrCreate(
    ['email' => 'admin@example.com'],
    [
        'name' => 'Admin User',
        'password' => Hash::make('password')
    ]
);

// Create default tenant
$tenant = $tenantService->createTenant([
    'name' => 'Default Hospital',
    'email' => 'admin@example.com',
], $user, 'professional');
```

### Option B: Using Seeder

Create a seeder:

```bash
php artisan make:seeder DefaultTenantSeeder
```

```php
<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultTenantSeeder extends Seeder
{
    public function run()
    {
        $tenantService = app(TenantService::class);

        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password')
            ]
        );

        $tenant = $tenantService->createTenant([
            'name' => 'Default Hospital',
            'email' => 'admin@example.com',
        ], $user, 'professional');

        $this->command->info("Default tenant created: {$tenant->slug}");
    }
}
```

Run seeder:
```bash
php artisan db:seed --class=DefaultTenantSeeder
```

## Step 3: Migrate Existing Data

Assign all existing records to the default tenant.

### Create Data Migration Script

```bash
php artisan make:migration migrate_existing_data_to_tenant
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Tenant;

return new class extends Migration
{
    public function up()
    {
        // Get the default tenant (first tenant or create one)
        $tenant = Tenant::first();
        
        if (!$tenant) {
            throw new \Exception('No tenant found. Please create a tenant first.');
        }

        // Update clinics
        \DB::table('clinics')
            ->whereNull('tenant_id')
            ->update(['tenant_id' => $tenant->id]);

        // Update queues
        \DB::table('queues')
            ->whereNull('tenant_id')
            ->update(['tenant_id' => $tenant->id]);

        // Update sub_queues
        \DB::table('sub_queues')
            ->whereNull('tenant_id')
            ->update(['tenant_id' => $tenant->id]);

        // Update users (set current_tenant_id)
        \DB::table('users')
            ->whereNull('current_tenant_id')
            ->update(['current_tenant_id' => $tenant->id]);

        // Create tenant_user relationships for existing users
        $users = \DB::table('users')->whereNull('current_tenant_id')->get();
        foreach ($users as $user) {
            \DB::table('tenant_users')->insertOrIgnore([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'role' => 'owner',
                'is_active' => true,
                'joined_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        // Revert changes if needed
        \DB::table('clinics')->update(['tenant_id' => null]);
        \DB::table('queues')->update(['tenant_id' => null]);
        \DB::table('sub_queues')->update(['tenant_id' => null]);
        \DB::table('users')->update(['current_tenant_id' => null]);
        \DB::table('tenant_users')->truncate();
    }
};
```

Run the migration:
```bash
php artisan migrate
```

## Step 4: Update Application Configuration

### Environment Variables

Add to `.env`:

```env
# Tenant Configuration
TENANT_DEFAULT_DOMAIN=localhost
TENANT_SUBDOMAIN_ENABLED=true
```

### Session Configuration

Ensure sessions are configured properly for multi-tenant:

```php
// config/session.php
'cookie' => env('SESSION_COOKIE', 'laravel_session'),
'domain' => env('SESSION_DOMAIN', null), // Set per tenant if needed
```

## Step 5: Test Tenant Isolation

Verify that data is properly isolated:

```bash
php artisan tinker
```

```php
// Create two tenants
$tenant1 = Tenant::create(['name' => 'Hospital 1', 'slug' => 'hospital1', ...]);
$tenant2 = Tenant::create(['name' => 'Hospital 2', 'slug' => 'hospital2', ...]);

// Set tenant 1
app()->instance('tenant', $tenant1);
app()->instance('tenant_id', $tenant1->id);

// Create clinic for tenant 1
$clinic1 = Clinic::create(['name' => 'Clinic 1', 'tenant_id' => $tenant1->id]);

// Switch to tenant 2
app()->instance('tenant', $tenant2);
app()->instance('tenant_id', $tenant2->id);

// Should not see tenant 1's clinic
$clinics = Clinic::all(); // Should be empty or only tenant 2's clinics
```

## Step 6: Update Frontend (if needed)

### Dashboard Updates

The dashboard now receives a `$tenant` variable. Update views if needed:

```blade
@if(isset($tenant))
    <h1>{{ $tenant->name }}</h1>
@endif
```

### Route Updates

All routes now require tenant context. Update any hardcoded routes:

```blade
<!-- Old -->
<a href="/queues/1">Queue</a>

<!-- New (tenant is automatically included) -->
<a href="{{ route('queues.index', ['clinicId' => 1]) }}">Queue</a>
```

## Step 7: User Onboarding

### For Existing Users

1. Users need to log in
2. If they have `current_tenant_id`, they'll be redirected to dashboard
3. If not, they'll see tenant selection page
4. They can select their tenant or register a new one

### For New Users

1. Register at `/register`
2. Creates both user account and tenant
3. User is automatically set as tenant owner
4. Tenant gets 14-day trial subscription

## Step 8: Subscription Setup

### Create Initial Subscriptions

For existing tenants, create subscriptions:

```php
use App\Models\Tenant;
use App\Services\TenantService;

$tenantService = app(TenantService::class);

Tenant::each(function ($tenant) use ($tenantService) {
    if (!$tenant->subscription) {
        $tenantService->createSubscription($tenant, 'professional');
    }
});
```

## Step 9: Testing

### Test Scenarios

1. **Tenant Isolation**
   - Create data in Tenant A
   - Switch to Tenant B
   - Verify Tenant B cannot see Tenant A's data

2. **Subscription Limits**
   - Create subscription with `max_clinics = 2`
   - Try to create 3 clinics
   - Verify limit is enforced

3. **User Access**
   - Add user to Tenant A
   - Try to access Tenant B
   - Verify access is denied

4. **Trial Expiration**
   - Set tenant's `trial_ends_at` to past date
   - Try to access dashboard
   - Verify redirect to subscription page

## Step 10: Deployment

### Production Checklist

- [ ] Run migrations
- [ ] Create default tenant
- [ ] Migrate existing data
- [ ] Test tenant isolation
- [ ] Configure subdomain/domain routing
- [ ] Set up SSL for custom domains
- [ ] Configure session domain
- [ ] Test authentication flow
- [ ] Test subscription flow
- [ ] Monitor error logs

### Subdomain Configuration

For subdomain-based tenant identification:

1. Configure wildcard DNS:
   ```
   *.example.com -> Your server IP
   ```

2. Configure web server (Nginx example):
   ```nginx
   server {
       server_name *.example.com;
       # ... rest of config
   }
   ```

3. Update `.env`:
   ```env
   APP_URL=https://example.com
   SESSION_DOMAIN=.example.com
   ```

## Rollback Plan

If you need to rollback:

1. Restore database backup
2. Remove tenant-related migrations:
   ```bash
   php artisan migrate:rollback --step=7
   ```
3. Remove new code files
4. Restore old routes/controllers

## Post-Migration Tasks

- [ ] Monitor tenant creation
- [ ] Track subscription usage
- [ ] Set up billing integration
- [ ] Configure analytics
- [ ] Update documentation
- [ ] Train users on new features

## Support

If you encounter issues during migration:

1. Check migration logs: `storage/logs/laravel.log`
2. Verify database constraints
3. Test tenant isolation
4. Review middleware registration
5. Check route definitions

## Common Issues

### Issue: "Tenant not found"
**Solution:** Ensure tenant exists and middleware is registered

### Issue: "Data visible across tenants"
**Solution:** Verify global scope is applied to models

### Issue: "Subscription errors"
**Solution:** Create subscription for tenant or extend trial period

### Issue: "User can't access tenant"
**Solution:** Add user to tenant via `tenant_users` table

