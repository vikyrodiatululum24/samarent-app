<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cetak extends Model
{
    protected $fillable = ['pengajuan_id', 'asuransi_id'];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function asuransi()
    {
        return $this->belongsTo(Asuransi::class);
    }
}
