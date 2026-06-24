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

    protected $casts = [
        'up' => 'array',
    ];

    protected static function booted()
    {
        static::saved(function ($model) {
            $model->up = json_encode($model->up);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
