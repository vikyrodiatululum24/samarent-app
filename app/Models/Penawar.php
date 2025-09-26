<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penawar extends Model
{
    protected $fillable = [
        'unit_jual_id',
        'nama',
        'no_wa',
        'harga_penawaran',
        'down_payment',
        'catatan',
    ];

    public function unitJual()
    {
        return $this->belongsTo(UnitJual::class, 'unit_jual_id');
    }
}
