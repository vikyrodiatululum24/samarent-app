<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penawar extends Model
{
    protected $table = 'penawars';

    protected $fillable = [
        'unit_jual_id',
        'nama_penawar',
        'harga_penawaran',
        'kontak_penawar',
        'status_penawaran',
    ];

    public function unit_jual()
    {
        return $this->belongsTo(UnitJual::class, 'unit_jual_id');
    }
}
