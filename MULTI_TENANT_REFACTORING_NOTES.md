# Multi-Tenant SaaS Refactoring Notes

## Overview
This document outlines the refactoring changes made to transform the hospital queue system into a proper multi-tenant SaaS architecture while preserving all existing functionality.

## Key Changes

### 1. Database Schema Fixes

#### Fixed sub_queues Foreign Key
- **Issue**: `sub_queues.clinic_id` was incorrectly referencing `queues.id` instead of `clinics.id`
- **Fix**: Created migration `2025_01_23_000001_fix_sub_queues_foreign_key_to_clinics.php`
- **Impact**: Sub-queues now correctly reference clinics, maintaining data integrity

#### Added Password to Clinics
- **Change**: Added `password` field to `clinics` table
- **Migration**: `2025_01_23_000002_add_password_to_clinics_table.php`
- **Security**: Passwords are automatically hashed using bcrypt via model events
- **Backward Compatibility**: Supports both hashed and plain text passwords during migration

### 2. Model Updates

#### Clinic Model
- Added `password` field to `$fillable`
- Added automatic password hashing in `booted()` method
- Added `verifyPassword()` method for secure password verification
- Added `subQueues()` relationship
- Password is hidden from serialization

#### SubQueue Model
- Updated `clinic()` relationship to correctly reference `Clinic` model
- Kept deprecated `queue()` relationship for backward compatibility
- Foreign key now correctly points to `clinics.id`

### 3. Route Standardization

#### Route Model Binding
- Added route model binding for `clinic` parameter in `AppServiceProvider`
- Routes automatically scope clinics by tenant
- Standardized all queue routes to use `{clinic}` instead of `{clinicId}`

#### Updated Routes
- `/queues/{clinic}` - Queue dashboard
- `/queues/{clinic}/next/{queueNumber}` - Move to next
- `/queues/{clinic}/previous/{queueNumber}` - Move to previous
- `/queues/{clinic}/reset/{queueNumber}` - Reset queue
- `/api/queue/{clinic}` - Live queue API

**Backward Compatibility**: Middleware supports both route model binding and legacy `clinicId` parameter.

### 4. Middleware Enhancements

#### New: EnsureTenantAccess Middleware
- Verifies user has access to the current tenant
- Checks tenant is active
- Validates user belongs to tenant
- Handles redirects for unauthorized access
- Registered as `tenant.access` middleware alias

#### Updated: AuthorizeQueueAccess Middleware
- Now supports both route model binding (`clinic`) and legacy (`clinicId`)
- Automatically validates clinic belongs to tenant
- Maintains password verification flow

### 5. Controller Updates

#### QueueController
- All methods now use route model binding: `index(Clinic $clinic)`
- Removed redundant tenant/clinic validation (handled by route binding)
- Simplified code by leveraging automatic scoping
- Maintained all existing functionality

### 6. Preserved Functionality

✅ **All queue operations preserved**:
- Next/Previous/Reset queue numbers
- Live queue updates (3-second polling)
- Text-to-speech announcements
- Second screen display
- OPD Lab module
- Password verification flow

✅ **Multi-tenancy features**:
- Tenant identification (subdomain, domain, session)
- Tenant-scoped data access
- Subscription-based access control
- Plan-based feature gating

## Migration Steps

### 1. Run Migrations
```bash
php artisan migrate
```

This will:
- Fix the `sub_queues` foreign key constraint
- Add `password` column to `clinics` table

### 2. Update Existing Data (if needed)

If you have existing clinics that need passwords:
```php
use App\Models\Clinic;
use Illuminate\Support\Facades\Hash;

$clinic = Clinic::find(1);
$clinic->password = 'your-password'; // Will be auto-hashed
$clinic->save();
```

### 3. Update Frontend Routes (if using JavaScript)

If your frontend JavaScript constructs URLs, update them to use the new route format:
- Old: `/queues/1/next/1`
- New: `/queues/1/next/1` (same, but route parameter is now `{clinic}` instead of `{clinicId}`)

The route names remain the same, so `route('queues.next', ['clinic' => 1, 'queueNumber' => 1])` will work.

## Backward Compatibility

### Route Parameters
- Middleware supports both `{clinic}` (route model binding) and `{clinicId}` (legacy)
- Controllers accept both formats
- Existing JavaScript/API calls will continue to work

### Password Verification
- Queue passwords: Already hashed (from previous security fixes)
- Clinic passwords: New feature, supports both hashed and plain text during migration
- Plain text support will be removed in a future version

### Database Relationships
- `SubQueue::queue()` relationship kept for backward compatibility (deprecated)
- Use `SubQueue::clinic()` for new code

## Testing Checklist

- [ ] Run migrations successfully
- [ ] Verify sub_queues foreign key points to clinics.id
- [ ] Test queue operations (Next/Previous/Reset)
- [ ] Test password verification for clinics
- [ ] Test tenant access middleware
- [ ] Verify route model binding works
- [ ] Test OPD Lab module
- [ ] Test second screen functionality
- [ ] Verify multi-tenant data isolation

## Security Improvements

1. **Clinic Passwords**: Now hashed using bcrypt
2. **Route Model Binding**: Automatic tenant scoping prevents unauthorized access
3. **Tenant Access Middleware**: Ensures users can only access their tenant's data
4. **Foreign Key Constraints**: Proper referential integrity

## Next Steps (Optional)

1. Remove deprecated `SubQueue::queue()` relationship after migration period
2. Remove plain text password support after all passwords are migrated
3. Add clinic password management UI
4. Add audit logging for clinic access
5. Consider adding clinic-level permissions/roles

## Notes

- All existing functionality is preserved
- No breaking changes to queue logic
- OPD Lab module unchanged
- Subscription system unchanged
- All middleware chains maintained

