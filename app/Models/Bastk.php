<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bastk extends Model
{
    protected $table = 'bastk';

    protected $fillable = [
        'kode',
        'kepada',
        'alamat',
        'no_hp',
        'unit_id',
        'tgl_serah',
        'tgl_kembali',
        'nama_penyerah',
        'nama_penerima',
        'kondisi_unit',
        'exchange',
        'keterangan',
    ];

    protected $casts = [
        'tgl_serah' => 'date',
        'tgl_kembali' => 'date',
        'kondisi_unit' => 'json',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(BastkItems::class, 'bastk_id', 'id');
    }

    public function dokumentasi()
    {
        return $this->hasOne(BastkDokumentasi::class, 'bastk_id', 'id');
    }
}
