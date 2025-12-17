<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class Clinic extends Model
{
    protected $fillable = [
        'name',
        'password',
        'password_hash',
        'password_migrated_at',
        'tenant_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
        
        // Password hashing is handled by setPasswordAttribute mutator
        // No need to hash in creating/updating events since mutator already handles it
    }

    /**
     * Set the password attribute - ensures it's always hashed when set directly
     * This method is called when assigning: $clinic->password = 'plaintext'
     * 
     * @param string|null $value
     */
    public function setPasswordAttribute(?string $value): void
    {
        if (empty($value)) {
            $this->attributes['password'] = null;
            return;
        }

        // If it's already a hash, store as-is (to prevent double-hashing)
        // This handles cases where the model is being updated with existing hashed password
        if (str_starts_with($value, '$2y$')) {
            $this->attributes['password'] = $value;
            return;
        }

        // For new plain text passwords, hash them immediately
        // This ensures all NEW passwords are hashed
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Verify the clinic password
     * Checks both password_hash (new) and password (legacy) fields for backward compatibility
     * Silently migrates plain-text passwords to password_hash on successful verification
     * 
     * @param string $plainPassword
     * @return bool
     */
    public function verifyPassword(string $plainPassword): bool
    {
        // Priority 1: Check new password_hash field if migrated
        if ($this->hasMigratedPassword() && !empty($this->password_hash)) {
            return Hash::check($plainPassword, $this->password_hash);
        }
        
        // Priority 2: Check legacy password field
        if (empty($this->password)) {
            return false;
        }
        
        // Check if password is hashed (bcrypt starts with $2y$)
        $isPasswordHashed = str_starts_with($this->password, '$2y$');
        $isValid = false;
        
        if ($isPasswordHashed) {
            $isValid = Hash::check($plainPassword, $this->password);
            
            // If verification succeeds with hashed password in legacy field, migrate to password_hash
            if ($isValid && !$this->hasMigratedPassword()) {
                $this->migratePasswordToHash($this->password);
            }
        } else {
            // Legacy: plain text comparison (for backward compatibility during migration)
            $isValid = $plainPassword === $this->password;
            
            // If verification succeeds with plain-text password, silently migrate it
            if ($isValid) {
                $this->migratePasswordToHash(Hash::make($plainPassword));
            }
        }
        
        return $isValid;
    }

    /**
     * Silently migrate password to password_hash field
     * Transaction-safe migration that stores hashed password and sets migration timestamp
     * 
     * @param string $hashedPassword The hashed password to store
     * @return void
     */
    protected function migratePasswordToHash(string $hashedPassword): void
    {
        // Only migrate if not already migrated
        if ($this->hasMigratedPassword()) {
            return;
        }

        // Use transaction to ensure atomicity
        try {
            DB::transaction(function () use ($hashedPassword) {
                // Refresh model to ensure we have latest state
                $this->refresh();
                
                // Double-check we're still not migrated (prevent race conditions)
                if (!$this->hasMigratedPassword()) {
                    $this->password_hash = $hashedPassword;
                    $this->password_migrated_at = now();
                    
                    // Save without triggering model events to avoid recursion
                    $this->saveQuietly();
                }
            });
        } catch (\Exception $e) {
            // Silently fail - migration is best-effort, don't interrupt user
            // Log error in production for monitoring
            if (app()->environment('production')) {
                \Log::warning('Password migration failed for clinic', [
                    'clinic_id' => $this->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Check if password has been migrated to password_hash field
     */
    public function hasMigratedPassword(): bool
    {
        return !empty($this->password_migrated_at);
    }

    /**
     * Check if password needs migration (has password but not migrated)
     */
    public function needsPasswordMigration(): bool
    {
        return !empty($this->password) && !$this->hasMigratedPassword();
    }

    /**
     * Check if the stored password (legacy field) is hashed
     */
    public function isPasswordHashed(): bool
    {
        if (empty($this->password)) {
            return false;
        }
        
        return str_starts_with($this->password, '$2y$');
    }

    /**
     * Get the tenant that owns this clinic
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get all queues for this clinic
     */
    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class);
    }

    /**
     * Get all sub-queues for this clinic
     */
    public function subQueues(): HasMany
    {
        return $this->hasMany(SubQueue::class);
    }
}
