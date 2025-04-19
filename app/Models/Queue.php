<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    use HasFactory;

    protected $fillable = ['display', 'password'];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
