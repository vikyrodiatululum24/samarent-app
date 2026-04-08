<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogMail extends Model
{
    protected $fillable = [
        'attendence_id',
        'end_user_id',
        'status',
        'error_message',
    ];

    public function attendence()
    {
        return $this->belongsTo(DriverAttendence::class, 'attendence_id');
    }

    public function endUser()
    {
        return $this->belongsTo(EndUser::class, 'end_user_id');
    }
}
