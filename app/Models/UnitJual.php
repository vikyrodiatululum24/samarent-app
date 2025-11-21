<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitJual extends Model
{
    protected $fillable = [
        'unit_id',
        'harga_jual',
        'harga_netto',
        'status',
        'harga_terjual',
        'bukti_pembayaran',
        'rateBody',
        'rateInterior',
        'keterangan',
        'foto_depan',
        'foto_belakang',
        'foto_kiri',
        'foto_kanan',
        'foto_interior',
        'foto_odometer',
        'odometer',
    ];

    protected $casts = [
        'bukti_pembayaran' => 'array',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function penawars()
    {
        return $this->hasMany(Penawar::class, 'unit_jual_id', 'id');
    }
}
