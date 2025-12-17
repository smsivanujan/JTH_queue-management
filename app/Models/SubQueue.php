<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubQueue extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'clinic_id',
        'queue_number',
        'current_number',
        'next_number',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    /**
     * Get the tenant that owns this sub-queue
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the clinic that owns this sub-queue
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Get the queue that this sub-queue belongs to (via clinic)
     * 
     * @deprecated Use clinic() relationship instead. This is kept for backward compatibility.
     */
    public function queue(): BelongsTo
    {
        return $this->belongsTo(Queue::class, 'clinic_id');
    }
}
