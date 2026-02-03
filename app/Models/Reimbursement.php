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
        'tujuan_perjalanan',
        'keterangan',
        'dana_masuk',
        'dana_keluar',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
