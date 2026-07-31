<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    protected $fillable = [
        'user_id',
        'pengajuan_id',
        'bukti_transaksi',
        'bukti_transaksi_2',
    ];

    protected $casts = [
        'bukti_transaksi' => 'string',
        'bukti_transaksi_2' => 'string',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
