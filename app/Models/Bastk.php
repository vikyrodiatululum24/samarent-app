<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bastk extends Model
{
    protected $table = 'bastk';

    protected $fillable = [
        'created_by',
        'no_bastk',
        'type_bastk',
        'jenis_bastk',
        'kode',
        'kepada',
        'alamat',
        'no_hp',
        'unit_id',
        'tgl_serah',
        'tgl_kembali',
        'nama_penyerah',
        'nama_penerima',
        'kondisi_unit',
        'exchange',
        'keterangan',
    ];

    protected $casts = [
        'tgl_serah' => 'date',
        'tgl_kembali' => 'date',
        'kondisi_unit' => 'json',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(BastkItems::class, 'bastk_id', 'id');
    }

    public function dokumentasi()
    {
        return $this->hasOne(BastkDokumentasi::class, 'bastk_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    static function boot()
    {
        parent::boot();

        static::creating(function ($bastk) {
            if (!$bastk->created_by) {
                $bastk->created_by = auth()->id();
            }
            if (!$bastk->no_bastk) {
                $bastk->no_bastk = $bastk->generateNoBastk($bastk->kode, $bastk->jenis_bastk);
            }
        });
    }

    public function generateNoBastk($kode, $jenisBastk)
    {
        $kode = strtoupper($kode); // Convert kode to uppercase
        $latestBastk = self::latest('id')->where('jenis_bastk', $jenisBastk)->first();
        
        $newNumber = 1;
        if ($latestBastk && $latestBastk->no_bastk) {
            // Contoh format: BASTK/0009/07/2026/NL
            // Kita pecah string berdasarkan "/"
            $parts = explode('/', $latestBastk->no_bastk);
            // Angka 4 digit ada di indeks ke-1 (setelah BASTK)
            if (isset($parts[1]) && is_numeric($parts[1])) {
                $newNumber = (int)$parts[1] + 1;
            }
        }

        $month = now()->format('m/Y'); // results in something like: 07/2026

        return 'BASTK/' . str_pad($newNumber, 4, '0', STR_PAD_LEFT)  . '/' . $month . '/' . $kode;
        //results in something like: BASTK/0010/07/2026/NL
    }
}
