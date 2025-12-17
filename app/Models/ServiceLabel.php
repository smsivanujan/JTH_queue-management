<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Service Label Model
 * 
 * Stores labels/options for a service (e.g., test types for OPD Lab, 
 * service categories for customer service, table numbers for restaurants).
 */
class ServiceLabel extends Model
{
    protected $fillable = [
        'service_id',
        'label',
        'color',
        'translations',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'translations' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the service that owns this label
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get translated label for a specific language
     */
    public function getTranslation(string $language = 'en'): string
    {
        if (empty($this->translations) || !isset($this->translations[$language])) {
            return $this->label;
        }

        return $this->translations[$language];
    }
}
