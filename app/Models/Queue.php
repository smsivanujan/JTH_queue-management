<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class Queue extends Model
{
    use HasFactory;

    protected $fillable = [
        'display',
        'type', // 'sequential' or 'range'
        'password',
        'password_hash',
        'password_migrated_at',
        'clinic_id',
        'tenant_id',
        'image_path',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
        
        // Enforce hashing for NEW passwords (only if password is being set/changed)
        // This ensures all new passwords are hashed, while existing hashed passwords are not double-hashed
        static::creating(function ($queue) {
            if (!empty($queue->password)) {
                // Only hash if it's not already a hash (bcrypt starts with $2y$)
                // For NEW passwords, they should be plain text, so we hash them
                if (!str_starts_with($queue->password, '$2y$')) {
                    $queue->password = Hash::make($queue->password);
                }
            }
        });
        
        static::updating(function ($queue) {
            // Only process if password field is being changed
            if ($queue->isDirty('password') && !empty($queue->password)) {
                // Only hash if it's not already a hash
                // For NEW passwords being set, they should be plain text, so we hash them
                if (!str_starts_with($queue->password, '$2y$')) {
                    $queue->password = Hash::make($queue->password);
                }
            }
        });
    }

    /**
     * Set the password attribute - ensures it's always hashed when set directly
     * This method is called when assigning: $queue->password = 'plaintext'
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
     * Verify the queue password
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
                \Log::warning('Password migration failed for queue', [
                    'queue_id' => $this->id,
                    'error' => $e->getMessage()
                ]);
                $this->refresh();
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
     * Get the tenant that owns this queue
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the clinic that owns this queue
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Get all sub-queues for this queue
     */
    public function subQueues(): HasMany
    {
        return $this->hasMany(SubQueue::class, 'clinic_id');
    }

    /**
     * Check if queue is range-based type
     */
    public function isRangeType(): bool
    {
        return $this->type === 'range';
    }

    /**
     * Check if queue is sequential type
     */
    public function isSequentialType(): bool
    {
        return $this->type === 'sequential' || empty($this->type);
    }
}
