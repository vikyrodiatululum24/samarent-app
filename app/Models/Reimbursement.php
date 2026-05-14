<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reimbursement extends Model
{
    protected $fillable = [
        'user_id',
        'km_awal',
        'foto_odometer_awal',
        'km_akhir',
        'foto_odometer_akhir',
        'nota',
        'type',
        'tujuan_perjalanan',
        'keterangan',
        'dana_masuk',
        'dana_keluar',
        'metode_pembayaran',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $reimbursement) {
            if ($reimbursement->dana_masuk === null || $reimbursement->dana_masuk === '') {
                $reimbursement->dana_masuk = 0;
            }

            if ($reimbursement->dana_keluar === null || $reimbursement->dana_keluar === '') {
                $reimbursement->dana_keluar = 0;
            }
        });
    }

    
    public function getFotoOdometerAwalUrlAttribute()
    {
        return $this->foto_odometer_awal ? asset('storage/' . $this->foto_odometer_awal) : null;
    }

    public function getFotoOdometerAkhirUrlAttribute()
    {
        return $this->foto_odometer_akhir ? asset('storage/' . $this->foto_odometer_akhir) : null;
    }

    public function getNotaUrlAttribute()
    {
        return $this->nota ? asset('storage/' . $this->nota) : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'user_id', 'user_id');
    }
}
