<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DriverAttendence extends Model
{
    protected $fillable = [
        'user_id', // masuk
        'driver_id', // masuk
        'project_id', // masuk
        'end_user_id', // masuk
        'unit_id', // masuk
        'date', // masuk
        'time_in', // masuk
        'start_km', // masuk
        'note', // masuk
        'location_in', // masuk
        'photo_in', // masuk

        'location_check', // check
        'photo_check', // check
        'time_check', // check

        'end_km', // keluar
        'time_out', // keluar
        'location_out', // keluar
        'photo_out', // keluar
        'is_complete',
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
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function confirmation()
    {
        return $this->morphOne(Confirmation::class, 'confirmable');
    }
    public function overtimePay()
    {
        return $this->hasMany(OvertimePay::class, 'driver_attendence_id');
    }
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
    
}
