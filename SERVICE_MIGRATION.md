# Service System Migration Guide

## Overview

This document describes the migration from hardcoded OPD Lab logic to a generic, data-driven Service queue system. The new system supports any queue-based business: hospitals, offices, restaurants, petrol sheds, banks, etc.

## What Changed

### New Models

1. **Service Model** (`app/Models/Service.php`)
   - Generic service configuration (name, type, password, settings)
   - Supports `range` (start-end) and `sequential` (single number) calling types
   - Tenant-scoped (multi-tenant support)

2. **ServiceLabel Model** (`app/Models/ServiceLabel.php`)
   - Labels/options for services (e.g., test types, service categories)
   - Multi-language support via translations JSON field
   - Color coding for display

### New Controllers

1. **ServiceController** (`app/Http/Controllers/ServiceController.php`)
   - Generic service queue management
   - Replaces hardcoded OPD Lab controller logic
   - Works with any service type

### New Events

1. **ServiceUpdated** (`app/Events/ServiceUpdated.php`)
   - Generic broadcast event for service updates
   - Replaces `OPDLabUpdated` for new services
   - Tenant-isolated WebSocket channels

### Deprecated Code (Maintained for Backward Compatibility)

- **OPDLabController**: Marked as `@deprecated`, kept for backward compatibility
- **OPDLabUpdated Event**: Still works, but new services should use `ServiceUpdated`
- **OPD Lab routes**: Still functional, but internally can redirect to Service routes

## Migration Steps

### 1. Run Migrations

```bash
php artisan migrate
```

This creates:
- `services` table
- `service_labels` table

### 2. Run Seeder (Optional - Creates OPD Lab Service)

```bash
php artisan db:seed --class=ServiceSeeder
```

This creates a default "OPD Lab" service for each tenant with:
- Range-based calling type
- Default test labels (Urine Test, FBC, ESR)
- Password migrated from config

### 3. Update Bootstrap (Middleware Registration)

Add service verification middleware to `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    // ... existing middleware ...
    
    $middleware->alias([
        // ... existing aliases ...
        'service.verify' => \App\Http\Middleware\VerifyServiceAccess::class,
    ]);
})
```

### 4. Test the System

1. Access a service: `/services/{service_id}`
2. Verify password (stored in service record)
3. Use the generic service UI

## Database Schema

### services Table

```sql
- id (primary key)
- tenant_id (foreign key)
- name (string) - e.g., "OPD Lab", "Customer Service"
- type (string) - 'range' or 'sequential'
- password_hash (string, nullable) - Hashed password for access
- password_migrated_at (timestamp, nullable)
- settings (json, nullable) - Service-specific settings
- is_active (boolean)
- timestamps
```

### service_labels Table

```sql
- id (primary key)
- service_id (foreign key)
- label (string) - e.g., "Urine Test", "Customer Service"
- color (string) - Display color (white, red, green, blue, etc.)
- translations (json, nullable) - Multi-language support
- sort_order (integer)
- is_active (boolean)
- timestamps
```

## Usage Examples

### Creating a New Service

```php
use App\Models\Service;
use App\Models\ServiceLabel;

// Create service
$service = Service::create([
    'tenant_id' => $tenant->id,
    'name' => 'Customer Service',
    'type' => 'sequential', // Single number calling
    'password_hash' => Hash::make('password123'),
    'is_active' => true,
]);

// Add labels/options
ServiceLabel::create([
    'service_id' => $service->id,
    'label' => 'General Inquiry',
    'color' => 'blue',
    'sort_order' => 1,
    'is_active' => true,
]);
```

### Accessing a Service

1. User navigates to `/services/{service_id}`
2. System checks if `service_{service_id}_verified` exists in session
3. If not, user must verify password via `/services/{service_id}/verify`
4. On successful verification, session is set and user can access service

### Frontend Integration

The generic `service.js` file handles:
- Range-based calling (start-end)
- Sequential calling (single number)
- Label selection
- WebSocket broadcasting
- Second screen updates

## Hardcoded Code Removed/Deprecated

### Removed from Views

- Hardcoded test labels in `opdLab.blade.php` → Now loaded from database via ServiceLabel
- Hardcoded test names in JavaScript → Now loaded from `serviceConfig.labels`

### Deprecated (Still Works)

- `OPDLabController::index()` → Use `ServiceController::index($service)` instead
- `OPDLabController::verifyPassword()` → Use `ServiceController::verifyPassword($service)` instead
- `OPDLabController::broadcastUpdate()` → Use `ServiceController::broadcastUpdate($service)` instead
- `opdLab.blade.php` view → Use `service/index.blade.php` instead
- `opdLab.js` → Use `service.js` instead

## Migration Strategy

### Phase 1: Dual System (Current)

- New Service system works alongside OPD Lab
- OPD Lab routes still functional
- New services use Service routes

### Phase 2: Migration (Future)

- Update navigation to show Services list instead of OPD Lab
- Migrate existing OPD Lab usage to Service routes
- Optionally remove OPD Lab routes

### Phase 3: Cleanup (Future)

- Remove deprecated OPD Lab code
- Remove hardcoded OPD Lab references
- Fully generic system

## Configuration

### Service Settings (JSON Field)

Example settings for different service types:

```json
{
  "display_mode": "tv", // "tv" or "desktop"
  "auto_advance": true,
  "announcement_enabled": true,
  "custom_colors": {
    "primary": "#3b82f6",
    "secondary": "#10b981"
  }
}
```

## Routes

### New Service Routes

- `GET /services/{service}` - Service queue page
- `POST /services/{service}/verify` - Verify password
- `GET /services/{service}/second-screen` - Second screen display
- `POST /services/{service}/broadcast` - Broadcast update

### Deprecated OPD Lab Routes (Still Work)

- `GET /opdLab` - OPD Lab page (deprecated)
- `GET /opd-lab` - OPD Lab page (deprecated)
- `POST /opd-lab/verify` - Verify password (deprecated)
- `GET /opd-lab/second-screen` - Second screen (deprecated)
- `POST /opd-lab/broadcast` - Broadcast update (deprecated)

## Testing

1. Create a test service:
   ```bash
   php artisan tinker
   $service = \App\Models\Service::create(['tenant_id' => 1, 'name' => 'Test Service', 'type' => 'range', 'password_hash' => Hash::make('test123'), 'is_active' => true]);
   ```

2. Access the service: `/services/{$service->id}`

3. Verify password and use the service

## Support

For questions or issues, refer to:
- Service models: `app/Models/Service.php` and `app/Models/ServiceLabel.php`
- Service controller: `app/Http/Controllers/ServiceController.php`
- Generic views: `resources/views/service/`

