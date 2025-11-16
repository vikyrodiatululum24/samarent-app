<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpenHouse extends Model
{
    protected $fillable = [
        'nama_event',
        'tanggal_event',
        'lokasi_event',
        'deskripsi_event',
        'waktu_mulai',
        'waktu_selesai',
        'is_active',
    ];
}
