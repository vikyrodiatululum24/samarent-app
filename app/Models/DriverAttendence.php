<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverAttendence extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'end_user_id',
        'unit_id',
        'date',
        'time_in',
        'start_km',
        'note',
        'location_in',
        'photo_in',
        'location_check',
        'photo_check',
        'end_km',
        'time_out',
        'location_out',
        'photo_out',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function endUser()
    {
        return $this->belongsTo(EndUser::class);
    }
}
