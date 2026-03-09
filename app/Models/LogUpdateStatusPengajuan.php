<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogUpdateStatusPengajuan extends Model
{
    protected $fillable = [
        'user_id',
        'pengajuan_id',
        'status_lama',
        'status_baru',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }
}
