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
        'tanggal_masuk_finance',
        'tanggal_otorisasi',
        'tanggal_pengerjaan',
        'status',
    ];

    protected $casts = [
        'tanggal_masuk_finance' => 'date',
        'tanggal_otorisasi' => 'date',
        'tanggal_pengerjaan' => 'date',
    ];

    public function service_unit()
    {
        return $this->hasMany(ServiceUnit::class, 'pra_pengajuan_id');
    }

}
