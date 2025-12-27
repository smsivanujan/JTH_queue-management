<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_cycle',
        'max_clinics',
        'max_users',
        'max_screens',
        'features',
        'trial_days',
        'is_active',
        'sort_order',
        'stripe_price_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'trial_days' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'max_clinics' => 'integer',
        'max_users' => 'integer',
        'max_screens' => 'integer',
    ];

    /**
     * Get all subscriptions for this plan
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Check if plan has a specific feature
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    /**
     * Check if plan allows unlimited clinics
     */
    public function hasUnlimitedClinics(): bool
    {
        return $this->max_clinics === -1;
    }

    /**
     * Check if plan allows unlimited users
     */
    public function hasUnlimitedUsers(): bool
    {
        return $this->max_users === -1;
    }

    /**
     * Check if plan allows unlimited screens
     */
    public function hasUnlimitedScreens(): bool
    {
        return $this->max_screens === -1;
    }

    /**
     * Get plan by slug
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->where('is_active', true)->first();
    }

    /**
     * Scope to get only active plans
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }
}

