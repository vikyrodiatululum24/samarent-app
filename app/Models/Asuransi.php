<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asuransi extends Model
{
    protected $table = 'asuransis';

    protected $fillable = [
        'unit_id',
        'up',
        'lokasi',
        'keterangan',
        'nama_pic',
        'tanggal_pengajuan',
        'tanggal_kejadian',
        'nama',
        'jenis',
        'periode_mulai',
        'periode_selesai',
        'nominal',
        'kategori',
        'status',
        'tujuan_pengajuan',
        'foto_ktp',
        'foto_sim',
        'foto_sntk',
        'foto_bpkb',
        'foto_polis_asuransi',
        'foto_ba',
        'foto_keterangan_bengkel',
        'foto_npwp_pt',
        'foto_unit',
        'foto_nota',
        'unit_pengganti_id',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'nominal' => 'decimal:2',
        'foto_unit' => 'array',
        'foto_nota' => 'array',
    ];

    /**
     * Relasi ke model Unit
     */
    public function unitPengganti(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_pengganti_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
