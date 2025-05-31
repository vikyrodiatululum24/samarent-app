<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    protected $fillable = [
        'user_id',
        'no_pengajuan',
        'nama',
        'no_wa',
        'project',
        'up',
        'up_lainnya',
        'provinsi',
        'kota',
        'keterangan',
        'payment_1',
        'bank_1',
        'norek_1',
        'keterangan_proses'
    ];

    protected static function booted()
    {
        static::creating(function ($pengajuan) {
            $nextId = static::max('id') + 1;
            $pengajuan->no_pengajuan = 'SPK/' . str_pad($nextId, 4, '0', STR_PAD_LEFT) . '/' . now()->format('m') . '/' . now()->format('Y');
        });
    }


    public function getFotoKondisiThumbnailAttribute()
    {
        return $this->foto_kondisi[0] ?? null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function complete()
    {
        return $this->hasone(Complete::class, 'pengajuan_id');
    }

    public function finance()
    {
        return $this->hasone(Finance::class, 'pengajuan_id');
    }

    public function service_unit()
    {
        return $this->hasMany(ServiceUnit::class, 'pengajuan_id');
    }
}
