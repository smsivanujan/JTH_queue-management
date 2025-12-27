<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'subject',
        'category',
        'message',
        'status',
        'priority',
        'admin_notes',
        'replied_at',
        'closed_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * Ticket categories
     */
    const CATEGORY_GENERAL = 'general';
    const CATEGORY_TECHNICAL = 'technical';
    const CATEGORY_BILLING = 'billing';
    const CATEGORY_FEATURE_REQUEST = 'feature_request';

    /**
     * Ticket statuses
     */
    const STATUS_OPEN = 'open';
    const STATUS_REPLIED = 'replied';
    const STATUS_CLOSED = 'closed';

    /**
     * Ticket priorities
     */
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /**
     * Get the tenant that owns this ticket
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user who created this ticket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if ticket is open
     */
    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    /**
     * Check if ticket is replied
     */
    public function isReplied(): bool
    {
        return $this->status === self::STATUS_REPLIED;
    }

    /**
     * Check if ticket is closed
     */
    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    /**
     * Mark ticket as replied
     */
    public function markAsReplied(): void
    {
        $this->update([
            'status' => self::STATUS_REPLIED,
            'replied_at' => now(),
        ]);
    }

    /**
     * Mark ticket as closed
     */
    public function markAsClosed(): void
    {
        $this->update([
            'status' => self::STATUS_CLOSED,
            'closed_at' => now(),
        ]);
    }

    /**
     * Reopen ticket
     */
    public function reopen(): void
    {
        $this->update([
            'status' => self::STATUS_OPEN,
            'replied_at' => null,
            'closed_at' => null,
        ]);
    }

    /**
     * Scope to get tickets by status
     */
    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get tickets by priority
     */
    public function scopeOfPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to get tickets by category
     */
    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
