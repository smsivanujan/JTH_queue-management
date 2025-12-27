<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'tenant_id',
        'subscription_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'issued_at',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'issued_at' => 'datetime',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Invoice statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Payment methods
     */
    const PAYMENT_METHOD_MANUAL = 'manual';
    const PAYMENT_METHOD_STRIPE = 'stripe';

    /**
     * Get the tenant that owns this invoice
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the subscription for this invoice
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Check if invoice is paid
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if invoice is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(string $paymentMethod = null): void
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'paid_at' => now(),
            'payment_method' => $paymentMethod ?? $this->payment_method,
        ]);
    }

    /**
     * Generate invoice number
     * Format: INV-YYYY-NNN (e.g., INV-2025-001)
     */
    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $lastInvoice = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice && preg_match('/INV-' . $year . '-(\d+)/', $lastInvoice->invoice_number, $matches)) {
            $number = (int)$matches[1] + 1;
        } else {
            $number = 1;
        }

        return 'INV-' . $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
