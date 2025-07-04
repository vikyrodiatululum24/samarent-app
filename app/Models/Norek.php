<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Norek extends Model
{
    protected $fillable = [
        'name',
        'norek',
        'bank',
    ];

    /**
     * Get the pengajuans associated with this norek.
     */
    public function pengajuans()
    {
        return $this->hasMany(Pengajuan::class, 'norek_id');
    }
}
