<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bengkel extends Model
{
    protected $fillable = [
        'nama',
        'keterangan',
        'provinsi',
        'kab_kota',
        'kecamatan',
        'desa',
        'alamat',
        'g_maps',
    ];

    // Relasi ke tabel kontak bengkel
    public function kontakBengkels()
    {
        return $this->hasMany(KontakBengkel::class);
    }

    // Accessor untuk mendapatkan alamat lengkap
    public function getAlamatLengkapAttribute()
    {
        $alamatParts = array_filter([
            $this->alamat,
            $this->desa,
            $this->kecamatan,
            $this->kab_kota,
            $this->provinsi,
        ]);

        return implode(', ', $alamatParts);
    }
}
