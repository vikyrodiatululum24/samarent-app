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
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($bosJoulmer) {
            if ($bosJoulmer->is_approved === 'approved') {
                $bosJoulmer->approved_at = now();
            }
        });

        static::updating(function ($bosJoulmer) {
            if ($bosJoulmer->is_approved === 'approved' && !$bosJoulmer->approved_at) {
                $bosJoulmer->approved_at = now();
            } elseif ($bosJoulmer->is_approved !== 'approved') {
                $bosJoulmer->approved_at = null;
            }
        });
    }

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
