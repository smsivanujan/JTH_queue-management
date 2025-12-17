<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

/**
 * Generic Service Model
 * 
 * Replaces hardcoded OPD Lab logic with a data-driven approach.
 * Supports any queue-based service: hospitals, offices, restaurants, petrol sheds, banks, etc.
 * 
 * @deprecated OPD Lab code - See ServiceController for generic implementation
 */
class Service extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'type', // 'range' or 'sequential'
        'password_hash',
        'password_migrated_at',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'password_migrated_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    /**
     * Get the tenant that owns this service
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
    
    /**
     * Get all labels for this service (e.g., test types, service categories)
     */
    public function labels(): HasMany
    {
        return $this->hasMany(ServiceLabel::class)->where('is_active', true)->orderBy('sort_order');
    }
    
    /**
     * Verify password for service access
     */
    public function verifyPassword(string $password): bool
    {
        if (empty($this->password_hash)) {
            return false;
        }

        return Hash::check($password, $this->password_hash);
    }

    /**
     * Set password (hashes automatically)
     */
    public function setPassword(string $password): void
    {
        $this->password_hash = Hash::make($password);
        $this->password_migrated_at = now();
        $this->save();
    }

    /**
     * Check if service uses range-based calling (start-end range)
     */
    public function isRangeType(): bool
    {
        return $this->type === 'range';
    }

    /**
     * Check if service uses sequential calling (single numbers)
     */
    public function isSequentialType(): bool
    {
        return $this->type === 'sequential';
    }
    
}
