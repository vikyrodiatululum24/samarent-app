<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    protected $fillable = [
        'user_id',
        'pengajuan_id',
        'bukti_transaksi',
    ];

    protected $casts = [
        'bukti_transaksi' => 'string',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }
}
