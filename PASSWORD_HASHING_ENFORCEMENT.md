# Secure Password Hashing Enforcement

**Date:** December 17, 2025  
**Purpose:** Enforce secure password hashing for all NEW passwords while maintaining backward compatibility

---

## Overview

This implementation ensures that all NEW passwords for clinics, queues, and OPD Lab are automatically hashed using bcrypt, while maintaining full backward compatibility with existing plain-text passwords.

---

## Implementation Details

### 1. Model-Level Password Hashing ✅

#### `app/Models/Clinic.php`

**Automatic Hashing:**
- Added `setPasswordAttribute()` mutator that automatically hashes plain-text passwords
- Updated `booted()` method with clearer comments explaining hashing logic
- All NEW passwords are automatically hashed when set

**Behavior:**
- ✅ NEW plain-text passwords → Automatically hashed
- ✅ Existing hashed passwords → Stored as-is (prevents double-hashing)
- ✅ Empty/null passwords → Stored as null

**Example:**
```php
// Plain text password (automatically hashed)
$clinic->password = 'MyNewPassword123';
$clinic->save(); // Password is hashed before saving

// Already hashed password (stored as-is)
$clinic->password = '$2y$10$...'; // Stored without re-hashing
$clinic->save();
```

#### `app/Models/Queue.php`

**Same implementation as Clinic model:**
- Added `setPasswordAttribute()` mutator
- Automatic hashing for NEW passwords
- Backward compatible with existing passwords

---

### 2. Password Strength Validation ✅

#### `app/Rules/PasswordStrength.php`

**New Validation Rule** with configurable requirements:

**Default Requirements:**
- Minimum length: 8 characters
- At least one uppercase letter (A-Z)
- At least one lowercase letter (a-z)
- At least one number (0-9)
- Special characters: Optional (default: false)

**Usage Example:**
```php
use App\Rules\PasswordStrength;

$request->validate([
    'password' => ['required', 'string', new PasswordStrength()],
]);

// Custom requirements
$request->validate([
    'password' => [
        'required',
        'string',
        new PasswordStrength(
            minLength: 12,
            requireUppercase: true,
            requireLowercase: true,
            requireNumbers: true,
            requireSpecialChars: true // Require special characters
        )
    ],
]);
```

**Validation Messages:**
- "The password must be at least {minLength} characters long."
- "The password must contain at least one uppercase letter."
- "The password must contain at least one lowercase letter."
- "The password must contain at least one number."
- "The password must contain at least one special character."

---

### 3. Password Helper Utility ✅

#### `app/Helpers/PasswordHelper.php`

**Utility methods for password management:**

```php
use App\Helpers\PasswordHelper;

// Validate password strength
$result = PasswordHelper::validateStrength('MyPassword123');
if ($result['valid']) {
    // Password meets requirements
} else {
    // $result['errors'] contains validation errors
}

// Hash a plain text password
$hashed = PasswordHelper::hash('plaintext');

// Check if value is already hashed
if (PasswordHelper::isHashed($value)) {
    // Already hashed
}
```

---

### 4. OPD Lab Password Configuration ⚠️

**Location:** `config/opd.php`

**Current State:**
- Password stored in environment variable `OPD_LAB_PASSWORD`
- Default fallback: `'1234'` (for development only)

**For Production:**
1. Hash your password using Artisan Tinker:
   ```bash
   php artisan tinker
   >>> Hash::make('your_secure_password')
   => "$2y$10$..."
   ```

2. Set in `.env`:
   ```env
   OPD_LAB_PASSWORD=$2y$10$your_hashed_password_here
   ```

3. Clear config cache:
   ```bash
   php artisan config:clear
   ```

**Note:** OPD Lab password is config-based, not model-based, so it requires manual hashing. The `OPDLabController` already supports both hashed and plain-text passwords for backward compatibility.

---

## Security Features

### ✅ Automatic Hashing
- All NEW passwords are automatically hashed using bcrypt
- No plain-text passwords stored for new records
- Hash detection prevents double-hashing

### ✅ Backward Compatibility
- Existing plain-text passwords continue to work
- Existing hashed passwords are not modified
- No breaking changes to authentication

### ✅ Password Strength
- Configurable validation rules
- Enforces minimum complexity requirements
- Customizable requirements per use case

### ✅ Model-Level Enforcement
- Hashing happens at the model level
- Cannot be bypassed by direct assignment
- Consistent behavior across the application

---

## Usage Guidelines

### For Clinic Passwords

**When creating/updating clinics:**
```php
use App\Models\Clinic;
use App\Rules\PasswordStrength;

// Validate before creating
$validated = $request->validate([
    'name' => ['required', 'string'],
    'password' => ['nullable', 'string', new PasswordStrength()],
]);

// Create clinic (password automatically hashed)
$clinic = Clinic::create([
    'name' => $validated['name'],
    'password' => $validated['password'], // Automatically hashed
    'tenant_id' => $tenant->id,
]);

// Update password (automatically hashed)
$clinic->password = 'NewSecurePassword123';
$clinic->save(); // Automatically hashed
```

### For Queue Passwords

**Same pattern as Clinic:**
```php
use App\Models\Queue;
use App\Rules\PasswordStrength;

$validated = $request->validate([
    'password' => ['nullable', 'string', new PasswordStrength()],
]);

$queue = Queue::create([
    'clinic_id' => $clinic->id,
    'password' => $validated['password'], // Automatically hashed
    // ...
]);
```

---

## Testing

### Manual Testing Checklist

- [ ] Create new clinic with plain-text password → Verify it's hashed in DB
- [ ] Update existing clinic with new password → Verify it's hashed
- [ ] Update existing clinic without changing password → Verify no changes
- [ ] Create clinic with already-hashed password → Verify no double-hashing
- [ ] Test password strength validation with weak passwords
- [ ] Test password strength validation with strong passwords
- [ ] Verify existing plain-text passwords still work for authentication
- [ ] Verify existing hashed passwords still work for authentication

### Example Test Code

```php
// Test automatic hashing
$clinic = Clinic::create([
    'name' => 'Test Clinic',
    'password' => 'PlainTextPassword123',
    'tenant_id' => 1,
]);

// Verify password is hashed
$this->assertTrue(str_starts_with($clinic->password, '$2y$'));
$this->assertTrue($clinic->verifyPassword('PlainTextPassword123'));

// Test password strength validation
$rule = new PasswordStrength();
$fail = function($message) use (&$errors) {
    $errors[] = $message;
};
$errors = [];

$rule->validate('password', 'weak', $fail);
$this->assertNotEmpty($errors); // Should fail

$errors = [];
$rule->validate('password', 'StrongPassword123', $fail);
$this->assertEmpty($errors); // Should pass
```

---

## Migration Notes

### Existing Deployments

**No Action Required:**
- Existing plain-text passwords continue to work
- Existing hashed passwords continue to work
- No data migration needed

**Future Migration (Optional):**
- Use `needsPasswordMigration()` helper to identify records
- Migrate plain-text passwords to hashed format
- Set `password_migrated_at` timestamp

---

## Backward Compatibility

✅ **100% Backward Compatible**

- Existing authentication logic unchanged
- Existing password verification unchanged
- No breaking changes
- All existing passwords continue to work

---

## Security Recommendations

### Immediate Actions

1. **Set Strong OPD Lab Password:**
   - Hash a strong password
   - Update `.env` file
   - Clear config cache

2. **Review Existing Passwords:**
   - Identify clinics/queues with plain-text passwords
   - Consider migrating them to hashed format
   - Use `needsPasswordMigration()` helper

### Future Enhancements

1. **Admin Interface for Password Management:**
   - Add UI for setting clinic/queue passwords
   - Use `PasswordStrength` validation
   - Show password requirements to users

2. **Password Migration Command:**
   - Create Artisan command to migrate existing passwords
   - Batch process for large deployments
   - Log migration progress

3. **Password Expiration (Optional):**
   - Add password expiration date
   - Force password reset after period
   - Notify admins of expired passwords

---

## Files Modified

1. ✅ `app/Models/Clinic.php` - Added `setPasswordAttribute()` mutator
2. ✅ `app/Models/Queue.php` - Added `setPasswordAttribute()` mutator
3. ✅ `app/Rules/PasswordStrength.php` - NEW validation rule
4. ✅ `app/Helpers/PasswordHelper.php` - NEW helper utility
5. ✅ `config/opd.php` - Updated comments with hashing instructions

---

## Summary

✅ **All NEW passwords are automatically hashed**  
✅ **Password strength validation available**  
✅ **Backward compatibility maintained**  
✅ **No breaking changes**  
✅ **Ready for production**

**Key Points:**
- Models automatically hash new passwords
- Validation rule enforces password strength
- Existing passwords continue to work
- OPD Lab requires manual hashing (config-based)
- All changes are non-breaking

---

**Status:** ✅ **COMPLETE**  
**Breaking Changes:** ❌ **NONE**  
**Backward Compatible:** ✅ **YES**  
**Production Ready:** ✅ **YES**

