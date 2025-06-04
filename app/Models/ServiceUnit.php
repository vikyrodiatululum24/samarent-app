<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ServiceUnit extends Model
{
    protected $fillable = [
        'pengajuan_id',
        'unit_id',
        'odometer',
        'service',
        'foto_unit',
        'foto_odometer',
        'foto_kondisi',
        'foto_pengerjaan_bengkel',
        'foto_tambahan',
    ];

    protected $casts = [
        'foto_unit' => 'string',
        'foto_odometer' => 'string',
        'foto_kondisi' => 'array',
        'foto_pengerjaan_bengkel' => 'string',
        'foto_tambahan' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($service_unit) {
            if ($service_unit->foto_unit) {
                Storage::disk('public')->delete($service_unit->foto_unit);
            }
            if ($service_unit->foto_pengerjaan_bengkel) {
                Storage::disk('public')->delete($service_unit->foto_pengerjaan_bengkel);
            }
            if ($service_unit->foto_odometer) {
                Storage::disk('public')->delete($service_unit->foto_odometer);
            }
            if ($service_unit->foto_pengerjaan_bengkel) {
                Storage::disk('public')->delete($service_unit->foto_pengerjaan_bengkel);
            }
            if (is_array($service_unit->foto_kondisi)) {
                foreach ($service_unit->foto_kondisi as $path) {
                    Storage::disk('public')->delete($path);
                }
            }
        });
    }

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
