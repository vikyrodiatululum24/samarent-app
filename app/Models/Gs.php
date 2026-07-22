<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gs extends Model
{
    protected $fillable = [
        'driver_id',
        'no_hp',
        'alasan',
        'project',
        'user',
        'no_hp_user',
        'lokasi',
        'unit_id',
        'jam_standby_mulai',
        'jam_standby_selesai',
        'tanggal_mulai',
        'tanggal_selesai',
        'kunci_unit',
        'keterangan',
        'driver_pengganti',
        'no_hp_pengganti',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    //relasi
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // scope
    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($query) use ($search) {
            $query->where('project', 'LIKE', "%$search%")
                ->orWhere('user', 'LIKE', "%$search%")
                ->orWhere('no_hp_user', 'LIKE', "%$search%")
                ->orWhere('lokasi', 'LIKE', "%$search%")
                ->orWhere('jam_standby_mulai', 'LIKE', "%$search%")
                ->orWhere('jam_standby_selesai', 'LIKE', "%$search%")
                ->orWhere('tanggal_mulai', 'LIKE', "%$search%")
                ->orWhere('tanggal_selesai', 'LIKE', "%$search%")
                ->orWhere('kunci_unit', 'LIKE', "%$search%")
                ->orWhere('keterangan', 'LIKE', "%$search%")
                ->orWhere('driver_pengganti', 'LIKE', "%$search%")
                ->orWhere('no_hp_pengganti', 'LIKE', "%$search%");
        });
    }
}
