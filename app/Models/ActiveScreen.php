<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Models\ScreenUsageLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ActiveScreen extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tenant_id',
        'clinic_id',
        'screen_type',
        'screen_token',
        'last_heartbeat_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'last_heartbeat_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    /**
     * Generate a unique screen token
     */
    public static function generateToken(): string
    {
        return Str::random(32) . '_' . time();
    }

    /**
     * Register a new active screen
     * 
     * @param int $tenantId
     * @param int|null $clinicId
     * @param string $screenType 'queue' or 'service'
     * @return self
     */
    public static function register(int $tenantId, ?int $clinicId, string $screenType): self
    {
        $screen = self::create([
            'tenant_id' => $tenantId,
            'clinic_id' => $clinicId,
            'screen_type' => $screenType,
            'screen_token' => self::generateToken(),
            'last_heartbeat_at' => now(),
        ]);

        // Create usage log for analytics (write-only, does not affect behavior)
        try {
            ScreenUsageLog::startSession($tenantId, $clinicId, $screenType, $screen->screen_token);
        } catch (\Exception $e) {
            // Silently fail - analytics should not break screen registration
            // Log error for monitoring but continue
            if (app()->environment('production')) {
                \Log::warning('Failed to create screen usage log', [
                    'error' => $e->getMessage(),
                    'screen_token' => $screen->screen_token,
                ]);
            }
        }

        return $screen;
    }

    /**
     * Update heartbeat for a screen token
     * 
     * @param string $screenToken
     * @return bool
     */
    public static function heartbeat(string $screenToken): bool
    {
        $screen = self::where('screen_token', $screenToken)->first();
        
        if (!$screen) {
            return false;
        }

        $screen->update(['last_heartbeat_at' => now()]);
        return true;
    }

    /**
     * Check if screen is still active (heartbeat within last 30 seconds)
     * 
     * @param int $timeoutSeconds
     * @return bool
     */
    public function isActive(int $timeoutSeconds = 30): bool
    {
        if (!$this->last_heartbeat_at) {
            return false;
        }

        return $this->last_heartbeat_at->greaterThan(now()->subSeconds($timeoutSeconds));
    }

    /**
     * Get active screens count for tenant
     * 
     * @param int $tenantId
     * @param string|null $screenType Optional filter by screen type
     * @param int $timeoutSeconds
     * @return int
     */
    public static function getActiveCount(int $tenantId, ?string $screenType = null, int $timeoutSeconds = 30): int
    {
        $query = self::where('tenant_id', $tenantId)
            ->where('last_heartbeat_at', '>=', now()->subSeconds($timeoutSeconds));

        if ($screenType) {
            $query->where('screen_type', $screenType);
        }

        return $query->count();
    }

    /**
     * Clean up expired screens (heartbeat older than timeout)
     * Also closes corresponding usage logs for analytics
     * 
     * @param int $timeoutSeconds
     * @return int Number of screens deleted
     */
    public static function cleanupExpired(int $timeoutSeconds = 30): int
    {
        // Close usage logs for expired screens before deleting
        try {
            ScreenUsageLog::closeInactiveSessions($timeoutSeconds);
        } catch (\Exception $e) {
            // Silently fail - analytics should not break cleanup
            if (app()->environment('production')) {
                \Log::warning('Failed to close inactive screen logs during cleanup', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return self::where('last_heartbeat_at', '<', now()->subSeconds($timeoutSeconds))
            ->orWhereNull('last_heartbeat_at')
            ->delete();
    }

    /**
     * Get the tenant that owns this screen
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the clinic associated with this screen (if queue screen)
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
