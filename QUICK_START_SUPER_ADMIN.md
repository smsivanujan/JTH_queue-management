# Quick Start: Set Up Super Admin

## Step 1: Run Migration (✅ Already Done)

The migration has been run successfully. The `is_super_admin` column is now in the `users` table.

## Step 2: Set a User as Super Admin

You have three options:

### Option 1: Using Tinker (Recommended)

```bash
php artisan tinker
```

Then in the tinker prompt:
```php
$user = \App\Models\User::where('email', 'your-email@example.com')->first();
$user->is_super_admin = true;
$user->save();
exit
```

### Option 2: Using SQL

```sql
UPDATE users SET is_super_admin = true WHERE email = 'your-email@example.com';
```

### Option 3: Using Laravel Query

Create a temporary route or add to a seeder:

```php
\App\Models\User::where('email', 'your-email@example.com')
    ->update(['is_super_admin' => true]);
```

## Step 3: Verify It's Working

1. **Login** as the user you set as super admin
2. **Check the dashboard** - You should see:
   - No "Upgrade Plan" messages
   - All "Add Clinic" and "Add Staff" buttons enabled
   - No limit warnings

3. **Test unlimited creation**:
   - Try creating clinics beyond your plan limit (should work)
   - Try adding staff beyond your plan limit (should work)
   - Access features that require plans (should work)

## What Changed?

✅ Super Admin can now:
- Bypass subscription checks
- Create unlimited clinics, staff, and services
- Use unlimited display screens
- Access all features (OPD Lab, services, etc.)

❌ Super Admin still respects:
- Tenant isolation (must belong to tenant)
- Role-based access control (RBAC) within tenants
- Tenant-level permissions

## Troubleshooting

If it's still not working:

1. **Clear caches** (already done):
   ```bash
   php artisan route:clear
   php artisan config:clear
   php artisan view:clear
   ```

2. **Check if user is actually super admin**:
   ```php
   php artisan tinker
   $user = \App\Models\User::where('email', 'your-email@example.com')->first();
   $user->is_super_admin; // Should return true
   $user->isSuperAdmin(); // Should return true
   ```

3. **Check browser cache** - Hard refresh (Ctrl+F5 or Cmd+Shift+R)

4. **Verify migration ran**:
   ```bash
   php artisan migrate:status
   ```
   Should show `2025_12_17_204706_add_is_super_admin_to_users_table` as "Ran"

