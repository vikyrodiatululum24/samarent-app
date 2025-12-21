<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormTugas extends Model
{
    protected $table = 'form_tugas';

    protected $fillable = [
        'user_id',
        'no_form',
        'nama_atasan',
        'penerima_tugas',
        'tanggal_mulai',
        'tanggal_selesai',
        'deskripsi',
        'unit_id',
        'lainnya',
        'sopir',
        'bbm',
        'toll',
        'penginapan',
        'uang_dinas',
        'entertaint_customer',
        'total',
        'pemohon',
    ];

    protected $casts = [
        'penerima_tugas' => 'array',
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    public function tujuanTugas()
    {
        return $this->hasMany(TujuanTugas::class, 'form_tugas_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
