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
            $bastk->created_by = auth()->id();
            $bastk->no_bastk = $bastk->generateNoBastk($bastk->kode);
        });
    }

    public function generateNoBastk($kode)
    {
        $kode = strtoupper($kode); // Convert kode to uppercase
        $latestBastk = self::latest('id')->first();
        $latestId = $latestBastk ? $latestBastk->id : 0;
        $newId = $latestId + 1;
        $month = now()->format('m/Y'); // results in something like: 07/2026

        return 'BASTK/' . str_pad($newId, 4, '0', STR_PAD_LEFT)  . '/' . $month . '/' . $kode;
        //results in something like: BASTK/0001/07/2026/BASTK
    }
}
