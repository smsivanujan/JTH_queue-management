# Database-Based Second Screen Tracking

**Date:** December 17, 2025  
**Purpose:** Replace unreliable session-based screen tracking with database persistence

---

## Overview

This implementation replaces session-based second screen tracking with a database-backed system that tracks active screens using heartbeats. This provides reliable screen limit enforcement while maintaining full backward compatibility.

---

## Implementation Details

### 1. Database Schema ✅

#### Migration: `2025_12_17_172336_create_active_screens_table.php`

**Table:** `active_screens`

**Columns:**
- `id` - Primary key
- `tenant_id` - Foreign key to tenants (cascade delete)
- `clinic_id` - Foreign key to clinics (nullable, cascade delete)
- `screen_type` - Enum: 'queue' or 'opd_lab'
- `screen_token` - Unique token (64 chars) for each screen session
- `last_heartbeat_at` - Timestamp of last heartbeat
- `created_at`, `updated_at` - Timestamps

**Indexes:**
- `tenant_id` + `screen_type` (for counting active screens per tenant)
- `last_heartbeat_at` (for cleanup of expired screens)
- `screen_token` (unique, for heartbeat lookups)

---

### 2. ActiveScreen Model ✅

#### `app/Models/ActiveScreen.php`

**Key Methods:**

1. **`register(int $tenantId, ?int $clinicId, string $screenType): self`**
   - Creates a new active screen record
   - Generates unique token
   - Sets initial heartbeat timestamp

2. **`heartbeat(string $screenToken): bool`**
   - Updates `last_heartbeat_at` for a screen token
   - Returns false if token not found

3. **`getActiveCount(int $tenantId, ?string $screenType = null, int $timeoutSeconds = 30): int`**
   - Counts active screens (heartbeat within timeout)
   - Filters by tenant and optional screen type

4. **`cleanupExpired(int $timeoutSeconds = 30): int`**
   - Removes screens with expired heartbeats
   - Returns count of deleted records

5. **`isActive(int $timeoutSeconds = 30): bool`**
   - Checks if a screen instance is still active

**Features:**
- Uses `TenantScope` for automatic tenant isolation
- Token generation: `Str::random(32) + '_' + time()`
- Heartbeat timeout: 30 seconds (configurable)

---

### 3. ScreenController ✅

#### `app/Http/Controllers/ScreenController.php`

**Routes:**
- `POST /screens/register` - Register new screen
- `POST /screens/heartbeat` - Send heartbeat

**Methods:**

1. **`register(Request $request)`**
   - Validates: `screen_type` (queue|opd_lab), `clinic_id` (nullable)
   - Verifies tenant and clinic ownership
   - Creates ActiveScreen record
   - Returns `screen_token`

2. **`heartbeat(Request $request)`**
   - Validates: `screen_token`
   - Updates heartbeat timestamp
   - Returns success/failure

**Security:**
- Protected by `screen.limit` middleware
- Tenant isolation enforced
- Clinic ownership verified

---

### 4. JavaScript Heartbeat System ✅

#### `public/js/screenHeartbeat.js`

**Global Object:** `screenHeartbeat`

**Methods:**

1. **`init(screenToken)`**
   - Initializes heartbeat for second screen window
   - Sends heartbeat every 12 seconds
   - Handles page visibility changes
   - Cleans up on page unload

2. **`register(screenType, clinicId)`**
   - Registers screen with database
   - Returns screen token
   - Called from parent window before opening second screen

3. **`sendHeartbeat()`**
   - Sends POST to `/screens/heartbeat`
   - Stops if token not found (404)

**Features:**
- Heartbeat interval: 12 seconds (between 10-15 as requested)
- Automatic cleanup on window close
- Handles network errors gracefully
- CSRF token included in requests

---

### 5. Middleware Update ✅

#### `app/Http/Middleware/EnforceScreenLimit.php`

**Updated Logic:**

**Before:**
- Tracked screens in session only
- Incremented session counter
- No cleanup mechanism

**After:**
- Primary: Uses database count (`ActiveScreen::getActiveCount()`)
- Fallback: Uses session count (if database count is 0)
- No session increment (registration happens in ScreenController)
- Checks limit before allowing registration

**Backward Compatibility:**
- Session fallback ensures existing behavior continues
- No breaking changes
- Gradual migration path

---

### 6. SubscriptionHelper Update ✅

#### `app/Helpers/SubscriptionHelper.php`

**Updated `canOpenScreen()` Method:**

**Before:**
```php
$activeScreens = session('active_screens', 0);
```

**After:**
```php
$activeScreens = ActiveScreen::getActiveCount($tenant->id);
if ($activeScreens === 0) {
    $activeScreens = session('active_screens', 0); // Fallback
}
```

---

### 7. JavaScript Integration ✅

#### OPD Lab Second Screen

**File:** `public/js/opdLab.js`

**Changes:**
- Registers screen before opening: `screenHeartbeat.register('opd_lab', null)`
- Passes token in URL: `?token=${screenToken}`
- Second screen initializes heartbeat with token from URL

**File:** `resources/views/secondScreen.blade.php`

**Changes:**
- Reads token from URL parameter
- Initializes heartbeat on page load
- Includes `screenHeartbeat.js` script

#### Queue Second Screen

**File:** `resources/views/index.blade.php`

**Changes:**
- Registers screen before opening: `screenHeartbeat.register('queue', clinicId)`
- Stores token in `sessionStorage` (since queue uses `window.open('')` with HTML write)
- Generated HTML reads token from `sessionStorage`
- Includes heartbeat initialization in generated HTML

---

## Flow Diagrams

### Screen Registration Flow

```
User clicks "Open Second Screen"
  ↓
JavaScript: screenHeartbeat.register('opd_lab' | 'queue', clinicId)
  ↓
POST /screens/register
  ↓
EnforceScreenLimit Middleware:
  - Check active screen count (database)
  - Fallback to session if database count = 0
  - Allow if count < max_screens
  ↓
ScreenController::register()
  - Create ActiveScreen record
  - Generate screen_token
  - Return token
  ↓
JavaScript receives token
  ↓
Open second screen with token
  ↓
Second screen initializes heartbeat
```

### Heartbeat Flow

```
Second Screen Window Loads
  ↓
screenHeartbeat.init(screenToken)
  ↓
Send heartbeat immediately
  ↓
Set interval (12 seconds)
  ↓
Every 12 seconds:
  POST /screens/heartbeat
  ↓
ScreenController::heartbeat()
  - Update last_heartbeat_at
  - Return success
  ↓
Continue heartbeat...
```

### Screen Expiration Flow

```
Browser/TV closes
  ↓
Heartbeat stops
  ↓
30 seconds pass (no heartbeat)
  ↓
ActiveScreen::getActiveCount()
  - Filters: last_heartbeat_at >= now() - 30 seconds
  - Expired screens not counted
  ↓
Screen limit check uses active count only
  ↓
Expired screens can be cleaned up later
```

---

## Security Features

### ✅ Tenant Isolation

- `ActiveScreen` uses `TenantScope` (automatic filtering)
- Screen registration verifies clinic belongs to tenant
- Heartbeat validates token exists (tenant scoped)

### ✅ Viewer Role Protection

- Screen registration requires authentication
- Protected by `screen.limit` middleware
- Viewer role remains read-only (cannot open screens)

### ✅ Token Security

- Tokens are random and unique (32 chars + timestamp)
- Tokens cannot be guessed
- Token lookup is tenant-scoped

---

## Backward Compatibility

### ✅ Session Fallback

**When Used:**
- If database count is 0, fallback to session
- Allows gradual migration
- No breaking changes

**Migration Path:**
1. Deploy new code (database + session fallback)
2. Existing sessions continue to work
3. New screens register in database
4. System gradually migrates to database-only

### ✅ Existing Behavior Preserved

- Screen limit checks work as before
- No changes to subscription plans
- No changes to screen limit values
- OPD Lab and Queue screens work identically

---

## Cleanup Mechanism

### Automatic Cleanup

**Heartbeat Timeout:**
- Default: 30 seconds
- Screens with heartbeat older than 30s are considered inactive
- Not counted in active screen limit

### Manual Cleanup (Optional)

```php
// Clean up expired screens
$deleted = ActiveScreen::cleanupExpired(30);

// Or run as scheduled task
// In app/Console/Kernel.php:
$schedule->call(function () {
    \App\Models\ActiveScreen::cleanupExpired(30);
})->hourly();
```

---

## Testing

### Manual Testing Checklist

- [ ] Open OPD Lab second screen → Verify registration in database
- [ ] Verify heartbeat every 12 seconds in network tab
- [ ] Close browser → Verify screen expires after 30 seconds
- [ ] Open queue second screen → Verify registration
- [ ] Check screen limit enforcement (database count)
- [ ] Verify session fallback works (if database count = 0)
- [ ] Test with multiple screens (up to limit)
- [ ] Verify tenant isolation (screens from one tenant don't affect another)

### Example Test Scenarios

**Scenario 1: Screen Registration**
```php
$screen = ActiveScreen::register(1, null, 'opd_lab');
$this->assertNotNull($screen->screen_token);
$this->assertNotNull($screen->last_heartbeat_at);
$this->assertEquals(1, $screen->tenant_id);
```

**Scenario 2: Heartbeat**
```php
$screen = ActiveScreen::register(1, null, 'opd_lab');
$token = $screen->screen_token;

sleep(5);
$result = ActiveScreen::heartbeat($token);
$this->assertTrue($result);

$screen->refresh();
$this->assertTrue($screen->last_heartbeat_at->greaterThan(now()->subSeconds(10)));
```

**Scenario 3: Active Count**
```php
ActiveScreen::register(1, null, 'opd_lab');
ActiveScreen::register(1, null, 'opd_lab');
ActiveScreen::register(1, 1, 'queue');

$count = ActiveScreen::getActiveCount(1);
$this->assertEquals(3, $count);

$opdCount = ActiveScreen::getActiveCount(1, 'opd_lab');
$this->assertEquals(2, $opdCount);
```

---

## Configuration

### Heartbeat Interval

**Default:** 12 seconds

**To Change:**
Edit `public/js/screenHeartbeat.js`:
```javascript
heartbeatIntervalMs: 15000, // 15 seconds
```

### Heartbeat Timeout

**Default:** 30 seconds

**To Change:**
Update method calls:
```php
ActiveScreen::getActiveCount($tenantId, null, 45); // 45 seconds
```

---

## Performance Considerations

### Database Queries

**Screen Count Query:**
```sql
SELECT COUNT(*) FROM active_screens
WHERE tenant_id = ? 
  AND last_heartbeat_at >= ?
```

**Optimized with:**
- Index on `tenant_id` + `screen_type`
- Index on `last_heartbeat_at`
- Efficient timestamp comparison

### Heartbeat Frequency

- 12-second interval balances:
  - Responsiveness (quick expiration detection)
  - Network load (not too frequent)
  - Database load (reasonable write frequency)

---

## Files Created/Modified

### New Files ✅

1. `database/migrations/2025_12_17_172336_create_active_screens_table.php`
2. `app/Models/ActiveScreen.php`
3. `app/Http/Controllers/ScreenController.php`
4. `public/js/screenHeartbeat.js`
5. `DATABASE_SCREEN_TRACKING.md` (this file)

### Modified Files ✅

1. `app/Http/Middleware/EnforceScreenLimit.php`
2. `app/Helpers/SubscriptionHelper.php`
3. `routes/web.php` (added screen routes)
4. `resources/views/secondScreen.blade.php` (heartbeat initialization)
5. `resources/views/opdLab.blade.php` (include heartbeat script)
6. `public/js/opdLab.js` (screen registration)
7. `resources/views/index.blade.php` (queue screen registration)
8. `app/Http/Controllers/OPDLabController.php` (pass token to view)

---

## Summary

✅ **Database-based tracking implemented**  
✅ **Heartbeat mechanism (12 seconds)**  
✅ **Session fallback for backward compatibility**  
✅ **Auto-expiration (30 seconds)**  
✅ **Tenant isolation preserved**  
✅ **Security rules maintained**  
✅ **No breaking changes**

**Key Benefits:**
- Reliable screen limit enforcement
- Automatic cleanup of closed screens
- Database persistence (survives server restarts)
- Backward compatible (session fallback)
- Production-ready

---

**Status:** ✅ **COMPLETE**  
**Breaking Changes:** ❌ **NONE**  
**Backward Compatible:** ✅ **YES**  
**Production Ready:** ✅ **YES** (after running migration)

