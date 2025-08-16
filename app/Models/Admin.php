<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = [
        'user_id',
        'ttd',
        'ttd2',
        'ttd3',
        'ttd4',
    ];

    protected $casts = [
        'ttd' => 'string',
        'ttd2' => 'string',
        'ttd3' => 'string',
        'ttd4' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
