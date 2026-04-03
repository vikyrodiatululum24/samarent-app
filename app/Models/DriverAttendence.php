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
        'end_km', // keluar
        'time_out', // keluar
        'location_out', // keluar
        'photo_out', // keluar
        'photo_in', // masuk
        'is_approved_in', // masuk
        'is_approved_out', // masuk
        'end_user_out', // keluar
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
        return $this->belongsTo(EndUser::class, 'end_user_id');
    }
    public function endUserOut()
    {
        return $this->belongsTo(EndUser::class, 'end_user_out');
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function confirmation()
    {
        return $this->morphMany(Confirmation::class, 'confirmable');
    }
    public function overtimePay()
    {
        return $this->hasMany(OvertimePay::class, 'driver_attendence_id');
    }
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
    public function checks()
    {
        return $this->hasMany(DriverCheck::class, 'attendance_id');
    }


}
