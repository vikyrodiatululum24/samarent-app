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
        'keterangan_proses'
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
    
}
