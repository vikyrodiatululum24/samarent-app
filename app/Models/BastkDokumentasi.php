<?php

namespace App\Models;

use App\Helpers\FileStorageHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BastkDokumentasi extends Model
{
    protected $table = 'bastk_dokumentasis';

    protected $fillable = [
        'bastk_id',
        'unit_depan',
        'unit_belakang',
        'unit_samping_kanan',
        'unit_samping_kiri',
        'kabin_depan',
        'kabin_tengah',
        'kabin_belakang',
        'dashboard',
        'odometer',
        'buku_service',
        'manual_book',
        'ban_serep',
        'stnk_depan',
        'stnk_belakang',
        'bastk',
        'kerusakan',
        'tools',
    ];

    protected $casts = [
        'kerusakan' => 'array',
        'tools' => 'array',
    ];

    public function bastk()
    {
        return $this->belongsTo(Bastk::class, 'bastk_id', 'id');
    }

     protected static function booted()
    {
        $fileFields = [
            'unit_depan',
            'unit_belakang',
            'unit_samping_kanan',
            'unit_samping_kiri',
            'kabin_depan',
            'kabin_tengah',
            'kabin_belakang',
            'dashboard',
            'odometer',
            'kerusakan',
            'tools',
            'buku_service',
            'manual_book',
            'ban_serep',
            'stnk_depan',
            'stnk_belakang',
            'bastk',
        ];

        static::updating(function ($model) use ($fileFields) {
            FileStorageHelper::deleteOldFiles($model, $fileFields);
        });

        static::deleting(function ($model) use ($fileFields) {
            FileStorageHelper::deleteModelFiles($model, $fileFields);
        });
    }
}
