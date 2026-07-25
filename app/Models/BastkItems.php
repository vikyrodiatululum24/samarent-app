<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BastkItems extends Model
{
    protected $table = 'bastk_items';

    protected $fillable = [
        'bastk_id',
        'kelengkapan',
        'baik',
        'rusak',
        'tidak_ada',
        'keterangan',
        'jenis_bbm',
        'bbm',
        'km',
    ];

    protected $casts = [
        'baik' => 'boolean',
        'rusak' => 'boolean',
        'tidak_ada' => 'boolean',
        'bbm' => 'integer',
        'km' => 'integer',
    ];

    public function bastk()
    {
        return $this->belongsTo(Bastk::class, 'bastk_id', 'id');
    }
}
