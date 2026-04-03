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
        'end_user_id',
        'approval_type',
        'used_at',
        'expires_at',
        'status',
    ];

    public function confirmable()
    {
        return $this->morphTo();
    }
}
