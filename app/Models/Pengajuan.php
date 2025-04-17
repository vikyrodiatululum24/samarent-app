<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Pengajuan extends Model
{
    protected $fillable = [
        'user_id',
        'no_pengajuan',
        'nama',
        'no_wa',
        'jenis',
        'type',
        'nopol',
        'odometer',
        'service',
        'project',
        'up',
        'up_lainnya',
        'provinsi',
        'kota',
        'keterangan',
        'payment_1',
        'bank_1',
        'norek_1',
        'foto_unit',
        'foto_odometer',
        'foto_kondisi',
    ];

    protected $casts = [
        'foto_unit' => 'string',
        'foto_odometer' => 'string',
        'foto_kondisi' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($pengajuan) {
            if ($pengajuan->foto_unit) {
                Storage::disk('public')->delete($pengajuan->foto_unit);
            }
            if ($pengajuan->foto_odometer) {
                Storage::disk('public')->delete($pengajuan->foto_odometer);
            }
            if (is_array($pengajuan->foto_kondisi)) {
                foreach ($pengajuan->foto_kondisi as $path) {
                    Storage::disk('public')->delete($path);
                }
            }
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
}
