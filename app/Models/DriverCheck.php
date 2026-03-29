<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverCheck extends Model
{
    protected $fillable = [
        'attendance_id',
        'location',
        'photo',
    ];

    public function attendance()
    {
        return $this->belongsTo(DriverAttendence::class, 'attendance_id');
    }
}
