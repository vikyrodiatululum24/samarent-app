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
    ];

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
