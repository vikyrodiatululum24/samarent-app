<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PraPengajuan extends Model
{
    protected $table = 'pra_pengajuans';

    protected $fillable = [
        'nama_pic',
        'no_wa',
        'project',
        'up',
        'up_lainnya',
        'provinsi',
        'kota',
        'unitId',
        'service',
        'tanggal',
        'tanggal_input_user',
        'tanggal_masuk_finance',
        'tanggal_otorisasi',
        'tanggal_pengerjaan',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_input_user' => 'date',
        'tanggal_masuk_finance' => 'date',
        'tanggal_otorisasi' => 'date',
        'tanggal_pengerjaan' => 'date',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unitId');
    }
}
