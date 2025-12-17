<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ScreenUsageLog extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tenant_id',
        'clinic_id',
        'screen_type',
        'screen_token',
        'started_at',
        'ended_at',
        'duration_seconds',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);

        // Calculate duration when ended_at is set
        static::saving(function ($log) {
            if ($log->ended_at && $log->started_at && (!$log->duration_seconds || $log->isDirty('ended_at'))) {
                $log->duration_seconds = $log->started_at->diffInSeconds($log->ended_at);
            }
        });
    }

    /**
     * Create a new usage log when screen is registered
     * 
     * @param int $tenantId
     * @param int|null $clinicId
     * @param string $screenType 'queue' or 'service'
     * @param string $screenToken
     * @return self
     */
    public static function startSession(int $tenantId, ?int $clinicId, string $screenType, string $screenToken): self
    {
        return self::create([
            'tenant_id' => $tenantId,
            'clinic_id' => $clinicId,
            'screen_type' => $screenType,
            'screen_token' => $screenToken,
            'started_at' => now(),
        ]);
    }

    /**
     * Close a usage log when screen becomes inactive
     * 
     * @param string $screenToken
     * @return bool True if log was found and closed
     */
    public static function endSession(string $screenToken): bool
    {
        $log = self::where('screen_token', $screenToken)
            ->whereNull('ended_at')
            ->first();

        if (!$log) {
            return false;
        }

        $log->ended_at = now();
        $log->duration_seconds = $log->started_at->diffInSeconds($log->ended_at);
        $log->save();

        return true;
    }

    /**
     * Close all inactive sessions (screens with expired heartbeats)
     * 
     * @param int $timeoutSeconds Heartbeat timeout (default: 30 seconds)
     * @return int Number of logs closed
     */
    public static function closeInactiveSessions(int $timeoutSeconds = 30): int
    {
        $cutoffTime = now()->subSeconds($timeoutSeconds);

        // Find active screens that have expired
        $expiredTokens = \App\Models\ActiveScreen::where('last_heartbeat_at', '<', $cutoffTime)
            ->orWhereNull('last_heartbeat_at')
            ->pluck('screen_token')
            ->toArray();

        if (empty($expiredTokens)) {
            return 0;
        }

        // Close usage logs for expired screens
        // Get logs and close them individually to trigger duration calculation
        $logsToClose = self::whereIn('screen_token', $expiredTokens)
            ->whereNull('ended_at')
            ->get();

        $closed = 0;
        $now = now();
        foreach ($logsToClose as $log) {
            $log->ended_at = $now;
            // Duration will be calculated in the saving event
            $log->save();
            $closed++;
        }

        return $closed;
    }

    /**
     * Get total screen hours for a tenant
     * 
     * @param int $tenantId
     * @param \DateTime|null $fromDate Optional start date
     * @param \DateTime|null $toDate Optional end date
     * @return float Total hours
     */
    public static function getTotalHours(int $tenantId, ?\DateTime $fromDate = null, ?\DateTime $toDate = null): float
    {
        $query = self::where('tenant_id', $tenantId)
            ->whereNotNull('ended_at')
            ->whereNotNull('duration_seconds');

        if ($fromDate) {
            $query->where('started_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->where('started_at', '<=', $toDate);
        }

        $totalSeconds = $query->sum('duration_seconds');

        return round($totalSeconds / 3600, 2); // Convert to hours
    }

    /**
     * Get active screens count for today
     * 
     * @param int $tenantId
     * @return int Count of active sessions today
     */
    public static function getActiveScreensToday(int $tenantId): int
    {
        return self::where('tenant_id', $tenantId)
            ->whereDate('started_at', today())
            ->whereNull('ended_at')
            ->count();
    }

    /**
     * Get usage breakdown by screen type
     * 
     * @param int $tenantId
     * @param \DateTime|null $fromDate Optional start date
     * @param \DateTime|null $toDate Optional end date
     * @return array ['queue' => hours, 'service' => hours]
     */
    public static function getUsageByType(int $tenantId, ?\DateTime $fromDate = null, ?\DateTime $toDate = null): array
    {
        $query = self::where('tenant_id', $tenantId)
            ->whereNotNull('ended_at')
            ->whereNotNull('duration_seconds');

        if ($fromDate) {
            $query->where('started_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->where('started_at', '<=', $toDate);
        }

        $breakdown = $query->selectRaw('screen_type, SUM(duration_seconds) as total_seconds')
            ->groupBy('screen_type')
            ->pluck('total_seconds', 'screen_type')
            ->toArray();

        // Convert to hours and ensure both types are present
        // Combine 'opd_lab' (legacy) with 'service' (new) for backward compatibility
        $serviceHours = round((($breakdown['service'] ?? 0) + ($breakdown['opd_lab'] ?? 0)) / 3600, 2);
        
        return [
            'queue' => round(($breakdown['queue'] ?? 0) / 3600, 2),
            'service' => $serviceHours,
        ];
    }

    /**
     * Get the tenant that owns this log
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the clinic associated with this log (if queue screen)
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
