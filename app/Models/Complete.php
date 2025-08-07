<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complete extends Model
{
    protected $fillable = [
        'user_id',
        'pengajuan_id',
        'bengkel_estimasi',
        'no_telp_bengkel',
        'nominal_estimasi',
        'kode',
        'tanggal_masuk_finance',
        'tanggal_tf_finance',
        'nominal_tf_finance',
        'payment_2',
        'bank_2',
        'norek_2',
        'nominal_tf_bengkel',
        'selisih_tf',
        'tanggal_tf_bengkel',
        'tanggal_pengerjaan',
        'bengkel_invoice',
        'status_finance',
        'foto_nota',
        'foto_tambahan',
    ];

    protected $casts = [
        'foto_nota' => 'array',
        'foto_tambahan' => 'array',
    ];


    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function pengajuan(): BelongsTo {
        return $this->belongsTo(Pengajuan::class);
    }
}
