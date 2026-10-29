<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AngsuranPengeluaran extends Model
{
    use HasFactory;

    protected $table = 'angsuran_pengeluarans';

    protected $fillable = [
        'angsuran_kendaraan_id',
        'tgl_pengeluaran',
        'jenis_pengeluaran',
        'nominal',
        'bukti_nota',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tgl_pengeluaran' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function angsuranKendaraan(): BelongsTo
    {
        return $this->belongsTo(AngsuranKendaraan::class, 'angsuran_kendaraan_id');
    }

    public function setorans(): HasMany
    {
        return $this->hasMany(AngsuranSetoran::class, 'angsuran_pengeluaran_id');
    }

    /**
     * Total nominal nota yang sudah dikompensasikan ke setoran harian.
     */
    public function getTotalTerkompensasiAttribute(): float
    {
        return (float) $this->setorans()
            ->whereIn('metode_setor', ['kompensasi_biaya', 'kombinasi'])
            ->sum('nominal');
    }

    /**
     * Sisa saldo nominal nota yang belum dikompensasikan.
     */
    public function getSisaKompensasiAttribute(): float
    {
        return max(0, (float) $this->nominal - $this->total_terkompensasi);
    }

    /**
     * Sinkronkan status pengeluaran berdasarkan sisa kompensasi.
     */
    public function sinkronkanStatus(): void
    {
        $sisa = $this->sisa_kompensasi;
        $totalTerkompensasi = $this->total_terkompensasi;

        if ($totalTerkompensasi <= 0) {
            if ($this->status === 'sudah_dikompensasi' || $this->status === 'dikompensasi_sebagian') {
                $this->update(['status' => 'disetujui']);
            }
        } elseif ($sisa <= 0) {
            $this->update(['status' => 'sudah_dikompensasi']);
        } else {
            $this->update(['status' => 'dikompensasi_sebagian']);
        }
    }

    protected static function booted(): void
    {
        static::deleting(function ($pengeluaran) {
            if ($pengeluaran->setorans()->exists()) {
                throw new \Exception('Data biaya service tidak dapat dihapus karena sudah terkompensasi ke setoran harian.');
            }
        });
    }
}
