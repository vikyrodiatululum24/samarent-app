<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'user_id',
        'password',
        'nik',
        'sim',
        'alamat',
        'no_wa',
        'tempat',
        'tanggal_lahir',
        'jenis_kelamin',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'agama',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
