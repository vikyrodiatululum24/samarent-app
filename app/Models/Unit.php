<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'jenis',
        'type',
        'nopol',
    ];

    protected $casts = [
        'jenis' => 'string',
        'type' => 'string',
        'nopol' => 'string',
    ];
}
