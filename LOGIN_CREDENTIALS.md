# Login Credentials Guide

## Overview

The SmartQueue Hospital Queue Management System uses Laravel's standard authentication. There are **no default login credentials** - you need to create a user account first.

## How to Create Login Credentials

### Option 1: Register a New Account (Recommended)

1. Visit the registration page: `/register` or click "Register" on the login page
2. Fill in the form:
   - **Organization Information**: Name, email, phone, address
   - **Your Account**: Name, email, password
3. Submit the form
4. You'll be automatically logged in and a tenant will be created for you

### Option 2: Create Account via Tinker (For Development)

```bash
php artisan tinker
```

```php
use App\Models\User;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Support\Facades\Hash;

// Create user account
$user = User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'), // Change this!
]);

// Create tenant and subscription
$tenantService = app(\App\Services\TenantService::class);
$tenant = $tenantService->createTenant([
    'name' => 'Your Hospital Name',
    'email' => 'admin@example.com',
], $user, 'professional');

echo "User created: {$user->email}\n";
echo "Password: password (change this immediately!)\n";
echo "Tenant: {$tenant->name}\n";
```

### Option 3: Use Database Seeder

```bash
php artisan db:seed --class=DatabaseSeeder
```

This creates a test user:
- **Email**: `test@example.com`
- **Password**: `password` (from UserFactory)

## Default Test Credentials (If Seeder Was Run)

If you ran the database seeder, you can use:

- **Email**: `test@example.com`
- **Password**: `password`

⚠️ **Warning**: These are default credentials. Change them immediately in production!

## How to Check Existing Users

### Using Tinker

```bash
php artisan tinker
```

```php
// List all users
\App\Models\User::all(['id', 'name', 'email'])->each(function($user) {
    echo "ID: {$user->id}, Email: {$user->email}, Name: {$user->name}\n";
});

// Find specific user
$user = \App\Models\User::where('email', 'admin@example.com')->first();
if ($user) {
    echo "User found: {$user->name}\n";
} else {
    echo "User not found\n";
}
```

### Using Database Query

```sql
SELECT id, name, email, current_tenant_id FROM users;
```

## Login Process

1. **Visit Login Page**: Go to `/login`
2. **Enter Credentials**: 
   - Email address
   - Password
3. **Optional**: Check "Remember me" to stay logged in
4. **Submit**: Click "Login" button

### After Login

- If user has a `current_tenant_id`, they'll be redirected to `/dashboard`
- If user has no tenant, they'll see the tenant selection page
- If user belongs to multiple tenants, they can switch between them

## Password Reset (If Needed)

Currently, there's no password reset functionality implemented. To reset a password manually:

```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'admin@example.com')->first();
$user->password = Hash::make('new_password_here');
$user->save();

echo "Password updated for {$user->email}\n";
```

## Security Notes

1. **No Default Credentials**: The system doesn't create default users automatically
2. **Password Hashing**: All passwords are hashed using bcrypt
3. **Change Default Passwords**: If you used default passwords from examples, change them immediately
4. **Production**: Use strong passwords in production environments

## Quick Setup Script

Create a user and tenant quickly:

```bash
php artisan tinker
```

```php
use App\Models\User;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Support\Facades\Hash;

// Create admin user
$user = User::firstOrCreate(
    ['email' => 'admin@hospital.com'],
    [
        'name' => 'Hospital Admin',
        'password' => Hash::make('SecurePassword123!')
    ]
);

// Create tenant with professional plan
$tenantService = app(TenantService::class);
$tenant = $tenantService->createTenant([
    'name' => 'Teaching Hospital Jaffna',
    'email' => 'admin@hospital.com',
], $user, 'professional');

echo "✅ Setup Complete!\n";
echo "Email: admin@hospital.com\n";
echo "Password: SecurePassword123!\n";
echo "Tenant: {$tenant->name}\n";
```

## Troubleshooting

### "These credentials do not match our records"
- User doesn't exist - create an account first
- Wrong email or password
- Check if user was created correctly

### "Tenant not identified" after login
- User doesn't have a `current_tenant_id`
- User doesn't belong to any tenant
- Visit `/tenant/select` to select or create a tenant

### Can't access dashboard
- Ensure user has an active subscription or is on trial
- Check if tenant is active
- Verify subscription hasn't expired

