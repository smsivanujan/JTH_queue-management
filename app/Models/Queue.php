<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    use HasFactory;

    protected $fillable = ['current_number', 'next_number'];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
