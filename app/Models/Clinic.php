<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    protected $fillable = ['name'];

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }
}
