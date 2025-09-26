<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitJual extends Model
{
    protected $fillable = [
        'unit_id',
        'harga_jual',
        'harga_netto',
        'keterangan',
        'foto_depan',
        'foto_belakang',
        'foto_kiri',
        'foto_kanan',
        'foto_interior',
        'foto_odometer',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function penawars()
    {
        return $this->hasMany(Penawar::class, 'unit_jual_id', 'id');
    }
}
