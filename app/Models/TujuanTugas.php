<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TujuanTugas extends Model
{
    protected $table = 'tujuan_tugas';

    protected $fillable = [
        'form_tugas_id',
        'tanggal',
        'tempat',
        'location',
        'keterangan',
    ];

    public function formTugas()
    {
        return $this->belongsTo(FormTugas::class, 'form_tugas_id');
    }
}
