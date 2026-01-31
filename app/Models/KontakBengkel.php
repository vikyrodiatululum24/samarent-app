<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontakBengkel extends Model
{
    protected $fillable = [
        'bengkel_id',
        'nama',
        'no_telp',
    ];


    public function bengkel()
    {
        return $this->belongsTo(Bengkel::class);
    }
}
