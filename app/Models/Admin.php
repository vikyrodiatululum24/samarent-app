<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = [
        'user_id',
        'ttd'
    ];

    protected $casts = [
        'ttd' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
