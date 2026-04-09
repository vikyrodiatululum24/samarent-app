<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BosJoulmer extends Model
{
    protected $fillable = [
        'user_id',
        'pengajuan_id',
        'is_approved',
        'note',
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
