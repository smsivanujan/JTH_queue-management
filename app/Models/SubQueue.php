<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubQueue extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'queue_number',
        'current_number',
        'next_number',
    ];
}
