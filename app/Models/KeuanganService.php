<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeuanganService extends Model
{
    protected $fillable = [
        'pengajuan_id',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }
}
