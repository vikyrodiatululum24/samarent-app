<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'address',
        'phone_number',
        'date_of_birth',
        'photo',
        'norek',
        'bank',
        'nama_rek',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
