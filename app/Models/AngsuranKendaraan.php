<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AngsuranKendaraan extends Model
{
    use HasFactory;

    protected $table = 'angsuran_kendaraans';

    protected $fillable = [
        'driver_id',
        'unit_id',
        'tgl_serah_terima',
        'tgl_jatuh_tempo_akhir',
        'jangka_waktu_tahun',
        'dp',
        'tgl_dp',
        'frekuensi_per_bulan',
        'angsuran_per_hari',
        'normal_angsuran',
        'nilai_kontrak',
        'deposit',
        'tgl_deposit',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tgl_serah_terima' => 'date',
        'tgl_jatuh_tempo_akhir' => 'date',
        'tgl_dp' => 'date',
        'tgl_deposit' => 'date',
        'jangka_waktu_tahun' => 'integer',
        'frekuensi_per_bulan' => 'integer',
        'dp' => 'decimal:2',
        'angsuran_per_hari' => 'decimal:2',
        'normal_angsuran' => 'decimal:2',
        'nilai_kontrak' => 'decimal:2',
        'deposit' => 'decimal:2',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function setorans(): HasMany
    {
        return $this->hasMany(AngsuranSetoran::class, 'angsuran_kendaraan_id');
    }

    public function pengeluarans(): HasMany
    {
        return $this->hasMany(AngsuranPengeluaran::class, 'angsuran_kendaraan_id');
    }

    /**
     * Hitung total setoran yang sudah masuk pada bulan dan tahun tertentu.
     */
    public function getTotalSetoranBulan(int $bulan, int $tahun): float
    {
        return (float) $this->setorans()
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->sum('nominal');
    }

    /**
     * Total seluruh setoran yang sudah terbayar selama masa kontrak.
     */
    public function getTotalSetoranKeseluruhanAttribute(): float
    {
        return (float) $this->setorans()->sum('nominal');
    }

    /**
     * Sisa kewajiban nilai kontrak.
     */
    public function getSisaKontrakAttribute(): float
    {
        return max(0, (float) $this->nilai_kontrak - $this->total_setoran_keseluruhan);
    }
}
