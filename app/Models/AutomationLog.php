<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationLog extends Model
{
    protected $fillable = [
        'tenant_id',
        'type',
        'subtype',
        'sent_at',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Automation types
     */
    const TYPE_TRIAL_REMINDER = 'trial_reminder';
    const TYPE_INACTIVITY_NUDGE = 'inactivity_nudge';
    const TYPE_PAYMENT_SUCCESS = 'payment_success';
    const TYPE_PAYMENT_FAILURE = 'payment_failure';

    /**
     * Get the tenant for this automation log
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Check if an email of this type was sent recently (within cooldown period in hours)
     */
    public static function wasSentRecently(int $tenantId, string $type, string $subtype = null, int $cooldownHours = 24): bool
    {
        $query = self::where('tenant_id', $tenantId)
            ->where('type', $type)
            ->where('sent_at', '>=', now()->subHours($cooldownHours));

        if ($subtype !== null) {
            $query->where('subtype', $subtype);
        }

        return $query->exists();
    }

    /**
     * Log that an automation email was sent
     */
    public static function logSent(int $tenantId, string $type, string $subtype = null, array $metadata = []): void
    {
        self::create([
            'tenant_id' => $tenantId,
            'type' => $type,
            'subtype' => $subtype,
            'sent_at' => now(),
            'metadata' => $metadata,
        ]);
    }
}

