<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    protected $fillable = [
        'user_id',
        'up',
        'perusahaan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
