<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'tenant_id',
        'plan_id',
        'plan_name', // Kept for backward compatibility
        'status',
        'max_clinics',
        'max_users',
        'max_screens',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Subscription statuses
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';
    const STATUS_TRIAL = 'trial';

    /**
     * Boot the model
     */
    protected static function booted(): void
    {
        static::creating(function ($subscription) {
            // Auto-populate plan_name from plan if not set
            if ($subscription->plan_id && !$subscription->plan_name) {
                $plan = Plan::find($subscription->plan_id);
                if ($plan) {
                    $subscription->plan_name = $plan->slug;
                }
            }
            
            // Auto-populate limits from plan if not set
            if ($subscription->plan_id && (!$subscription->max_clinics || !$subscription->max_users || !$subscription->max_screens)) {
                $plan = Plan::find($subscription->plan_id);
                if ($plan) {
                    $subscription->max_clinics = $subscription->max_clinics ?? $plan->max_clinics;
                    $subscription->max_users = $subscription->max_users ?? $plan->max_users;
                    $subscription->max_screens = $subscription->max_screens ?? $plan->max_screens;
                    $subscription->features = $subscription->features ?? $plan->features;
                }
            }
        });
    }

    /**
     * Get the tenant that owns this subscription
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the plan for this subscription
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && ($this->ends_at === null || $this->ends_at->isFuture());
    }

    /**
     * Check if subscription has expired
     */
    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED
            || ($this->ends_at !== null && $this->ends_at->isPast());
    }

    /**
     * Check if subscription has a specific feature
     */
    public function hasFeature(string $feature): bool
    {
        // Check subscription features first
        if (in_array($feature, $this->features ?? [])) {
            return true;
        }
        
        // Fall back to plan features
        if ($this->plan) {
            return $this->plan->hasFeature($feature);
        }
        
        return false;
    }

    /**
     * Cancel the subscription
     */
    public function cancel(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Renew the subscription
     */
    public function renew(\DateTime $newEndDate): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'ends_at' => $newEndDate,
            'cancelled_at' => null,
        ]);
    }

    /**
     * Mark subscription as expired
     */
    public function expire(): void
    {
        $this->update([
            'status' => self::STATUS_EXPIRED,
        ]);
    }
}

