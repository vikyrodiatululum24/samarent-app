<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AngsuranSetoran extends Model
{
    use HasFactory;

    protected $table = 'angsuran_setorans';

    protected $fillable = [
        'angsuran_kendaraan_id',
        'tgl_setor',
        'periode_bulan',
        'periode_tahun',
        'nominal',
        'metode_setor',
        'angsuran_pengeluaran_id',
        'bukti_setor',
        'keterangan',
    ];

    protected $casts = [
        'tgl_setor' => 'date',
        'periode_bulan' => 'integer',
        'periode_tahun' => 'integer',
        'nominal' => 'decimal:2',
    ];

    public function angsuranKendaraan(): BelongsTo
    {
        return $this->belongsTo(AngsuranKendaraan::class, 'angsuran_kendaraan_id');
    }

    public function pengeluaran(): BelongsTo
    {
        return $this->belongsTo(AngsuranPengeluaran::class, 'angsuran_pengeluaran_id');
    }
}
