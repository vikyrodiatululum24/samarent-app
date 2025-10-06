<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Confirmation extends Model
{
    protected $fillable = [
        'token',
        'confirmable_type',
        'confirmable_id',
        'is_confirmed',
        'used_at',
        'expires_at',
    ];

    public function confirmable()
    {
        return $this->morphTo();
    }
}
