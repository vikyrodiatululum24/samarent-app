<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bbm extends Model
{
    protected $fillable = [
        'tanggal',
        'unit_id',
        'barcode_bbm'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
