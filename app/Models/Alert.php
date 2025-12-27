<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $fillable = [
        'type',
        'severity',
        'tenant_id',
        'title',
        'message',
        'last_triggered_at',
        'resolved_at',
        'metadata',
    ];

    protected $casts = [
        'last_triggered_at' => 'datetime',
        'resolved_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Alert types
     */
    const TYPE_CHURN_RISK = 'churn_risk';
    const TYPE_PAYMENT_RISK = 'payment_risk';
    const TYPE_SYSTEM_HEALTH = 'system_health';

    /**
     * Alert severities
     */
    const SEVERITY_LOW = 'low';
    const SEVERITY_MEDIUM = 'medium';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_CRITICAL = 'critical';

    /**
     * Get the tenant for this alert (nullable for system-wide alerts)
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Check if alert is resolved
     */
    public function isResolved(): bool
    {
        return $this->resolved_at !== null;
    }

    /**
     * Mark alert as resolved
     */
    public function markAsResolved(): void
    {
        $this->update(['resolved_at' => now()]);
    }

    /**
     * Mark alert as unresolved
     */
    public function markAsUnresolved(): void
    {
        $this->update(['resolved_at' => null]);
    }

    /**
     * Update last triggered timestamp
     */
    public function updateLastTriggered(): void
    {
        $this->update(['last_triggered_at' => now()]);
    }

    /**
     * Check if alert was triggered in last 24 hours (to prevent spam)
     */
    public function wasTriggeredRecently(): bool
    {
        if (!$this->last_triggered_at) {
            return false;
        }

        return $this->last_triggered_at->greaterThan(now()->subHours(24));
    }

    /**
     * Find or create alert by type and tenant
     */
    public static function findOrCreateByType(string $type, ?int $tenantId, array $attributes): self
    {
        return self::firstOrCreate(
            [
                'type' => $type,
                'tenant_id' => $tenantId,
            ],
            array_merge($attributes, [
                'last_triggered_at' => now(),
            ])
        );
    }

    /**
     * Scope to get only unresolved alerts
     */
    public function scopeUnresolved($query)
    {
        return $query->whereNull('resolved_at');
    }

    /**
     * Scope to get alerts by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get alerts by severity
     */
    public function scopeOfSeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }
}
