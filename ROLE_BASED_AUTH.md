# Role-Based Authentication Documentation

## Overview

This Laravel SaaS application implements role-based access control (RBAC) where users belong to tenants and have specific roles that determine their permissions.

## Roles

The system supports the following roles:

- **admin** - Full administrative access to all features
- **reception** - Can manage queues and patient flow
- **doctor** - Can manage queues and access lab
- **lab** - Can access OPD Lab functionality
- **viewer** - Read-only access (can view queues but cannot manage them)

## Role Permissions

### Admin
- ✅ Manage queues
- ✅ Access OPD Lab
- ✅ Manage subscriptions and plans
- ✅ Access all features

### Reception
- ✅ Manage queues (next, previous, reset)
- ❌ Access OPD Lab
- ❌ Manage subscriptions

### Doctor
- ✅ Manage queues
- ✅ Access OPD Lab
- ❌ Manage subscriptions

### Lab
- ❌ Manage queues
- ✅ Access OPD Lab
- ❌ Manage subscriptions

### Viewer
- ✅ View queues (read-only)
- ❌ Manage queues
- ❌ Access OPD Lab
- ❌ Manage subscriptions

## Implementation

### Role Enum

Location: `app/Enums/Role.php`

```php
use App\Enums\Role;

// Get role label
Role::ADMIN->label(); // "Administrator"

// Check role capabilities
Role::ADMIN->canManageQueues(); // true
Role::VIEWER->isViewOnly(); // true
```

### User Model Methods

Location: `app/Models/User.php`

```php
$user = auth()->user();

// Get role in current tenant
$role = $user->getCurrentRole(); // 'admin', 'reception', etc.

// Check if user has role(s)
$user->hasRole('admin'); // true/false
$user->hasRole(['admin', 'doctor']); // true if user has any of these roles

// Check specific capabilities
$user->isAdmin();
$user->canManageQueues();
$user->canAccessLab();
```

### Middleware

#### Role Middleware

Location: `app/Http/Middleware/EnsureUserHasRole.php`

Usage in routes:
```php
Route::middleware('role:admin')->group(function () {
    // Admin-only routes
});

Route::middleware('role:admin,doctor')->group(function () {
    // Routes accessible by admin OR doctor
});
```

### Blade Directives

Location: `app/Providers/BladeServiceProvider.php`

#### Check Role
```blade
@role('admin')
    <p>This is only visible to admins</p>
@endrole

@role('admin,doctor')
    <p>Visible to admin or doctor</p>
@endrole
```

#### Check Admin
```blade
@admin
    <p>Admin-only content</p>
@endadmin
```

#### Check Capabilities
```blade
@canManageQueues
    <button>Manage Queue</button>
@endcanManageQueues

@canAccessLab
    <a href="{{ route('opd.lab') }}">OPD Lab</a>
@endcanAccessLab

@viewer
    <p>Viewer can only see this</p>
@endviewer
```

### Helper Functions

Location: `app/Helpers/RoleHelper.php`

```php
use App\Helpers\RoleHelper;

// Get current user's role
RoleHelper::currentRole();

// Check if current user has role
RoleHelper::hasRole('admin');

// Get role label
RoleHelper::roleLabel('admin'); // "Administrator"

// Get all available roles
RoleHelper::allRoles();
```

## Route Protection

### Current Route Protection

1. **Dashboard** - All authenticated users (content varies by role)
2. **Queue Management** - All authenticated users can view, but only `admin`, `reception`, `doctor` can manage
3. **OPD Lab** - Only `admin`, `lab`, `doctor` can access
4. **Subscriptions/Plans** - Only `admin` can access

### Example Route Groups

```php
// Admin-only routes
Route::middleware(['auth', 'tenant', 'role:admin'])->group(function () {
    Route::get('/subscription', [SubscriptionController::class, 'index']);
});

// Queue management (viewable by all, manageable by specific roles)
Route::middleware(['auth', 'tenant', 'queue.auth'])->group(function () {
    // View route - all authenticated users
    Route::get('/queues/{clinic}', [QueueController::class, 'index']);
    
    // Management routes - only admin, reception, doctor
    Route::middleware('role:admin,reception,doctor')->group(function () {
        Route::post('/queues/{clinic}/next/{queueNumber}', [QueueController::class, 'next']);
    });
});
```

## Database Schema

### tenant_users Table

The `tenant_users` pivot table stores user-tenant relationships with roles:

```php
Schema::create('tenant_users', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('role')->default('viewer'); // admin, reception, doctor, lab, viewer
    $table->boolean('is_active')->default(true);
    $table->timestamp('joined_at')->useCurrent();
    $table->timestamps();
    
    $table->unique(['tenant_id', 'user_id']);
});
```

## Queue Authentication

The queue system has a two-tier authentication:

1. **Password Verification** - Users can verify clinic password to access queues
2. **Role-Based Access** - Users with queue management roles can access without password

The `AuthorizeQueueAccess` middleware handles this:

- Checks if user has verified password (stored in session)
- If not, checks if user has queue management role (`admin`, `reception`, `doctor`)
- If neither, redirects to password check page

## Examples

### In Controllers

```php
public function index()
{
    $user = auth()->user();
    
    // Check role
    if ($user->hasRole('admin')) {
        // Admin-specific logic
    }
    
    // Check capability
    if ($user->canManageQueues()) {
        // Show management buttons
    }
}
```

### In Blade Views

```blade
<div class="queue-actions">
    @canManageQueues
        <button onclick="nextQueue()">Next</button>
        <button onclick="previousQueue()">Previous</button>
        <button onclick="resetQueue()">Reset</button>
    @endcanManageQueues
    
    @viewer
        <p class="text-muted">You have read-only access to this queue.</p>
    @endviewer
</div>

@admin
    <a href="{{ route('subscription.index') }}">Manage Subscription</a>
@endadmin

@canAccessLab
    <a href="{{ route('opd.lab') }}">OPD Lab</a>
@endcanAccessLab
```

## Preserving Existing Functionality

The role-based authentication system is designed to preserve existing functionality:

1. **Password Verification** - Still works for users without specific roles
2. **Queue Access** - All authenticated users can view queues (preserving existing behavior)
3. **Queue Management** - Only restricted to specific roles for management actions
4. **Backward Compatibility** - Existing routes and middleware continue to work

## Testing

To test role-based access:

1. Assign roles to users in the `tenant_users` table
2. Test route access with different roles
3. Verify Blade directives show/hide content correctly
4. Ensure password verification still works for non-role users

## Migration Notes

When migrating existing users:

- Users with `owner` role should be migrated to `admin`
- Users with `user` role should be migrated to `viewer` or `reception` based on their usage
- Review user permissions and assign appropriate roles

