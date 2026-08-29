<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KunciSerep extends Model
{
    protected $fillable = [
        'unit_id', //relasi ke data_units
        'no_kunci', //dibuat opsional
        'lokasi', //lokasi box
        'status_kunci', //status kunci tersidia/dipakai
        'tanggal_masuk', //tanggal masuk
        'tanggal_keluar', //tanggal keluar
        'diambil_oleh', //diambil oleh
        'keterangan', //keterangan
    ];

    //relasi
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // scope
    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($query) use ($search) {
            $query->where('no_kunci', 'LIKE', "%$search%")
                ->orWhere('lokasi', 'LIKE', "%$search%")
                ->orWhere('status_kunci', 'LIKE', "%$search%")
                ->orWhere('tanggal_masuk', 'LIKE', "%$search%")
                ->orWhere('tanggal_keluar', 'LIKE', "%$search%")
                ->orWhere('diambil_oleh', 'LIKE', "%$search%")
                ->orWhere('keterangan', 'LIKE', "%$search%");
        });
    }
}
