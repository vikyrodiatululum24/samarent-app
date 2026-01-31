<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $table = 'wilayah';

    protected $fillable = [
        'kode',
        'nama',
        'level',
        'parent_id',
    ];

    public $timestamps = false;

    // Relasi ke parent (wilayah induk)
    public function parent()
    {
        return $this->belongsTo(Wilayah::class, 'parent_id');
    }

    // Relasi ke children (wilayah anak)
    public function children()
    {
        return $this->hasMany(Wilayah::class, 'parent_id');
    }

    // Scope untuk filter berdasarkan level
    public function scopeProvinsi($query)
    {
        return $query->where('level', 'provinsi');
    }

    public function scopeKabupaten($query)
    {
        return $query->where('level', 'kabupaten');
    }

    public function scopeKecamatan($query)
    {
        return $query->where('level', 'kecamatan');
    }

    public function scopeDesa($query)
    {
        return $query->where('level', 'desa');
    }
}
