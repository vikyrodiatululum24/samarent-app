<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;


class DriverAttendence extends Model
{
    protected $fillable = [
        'user_id', // masuk
        'driver_id', // masuk
        'project_id', // masuk
        'end_user_id', // masuk
        'end_user_out', // keluar
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
        'is_complete',
        'shift', // masuk
        'note_admin', // masuk
    ];

    protected $casts = [
        'date' => 'date',
        'is_complete' => 'boolean',
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

    protected static function booted()
    {
        static::updating(function (self $model) {
            if ($model->isDirty('photo_in')) {
                $old = $model->getOriginal('photo_in');
                if (! empty($old)) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $old));
                }
            }

            if ($model->isDirty('photo_out')) {
                $old = $model->getOriginal('photo_out');
                if (! empty($old)) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $old));
                }
            }
        });

        static::deleting(function (self $model) {
            if (! empty($model->photo_in)) {
                Storage::disk('public')->delete(str_replace('storage/', '', $model->photo_in));
            }
            if (! empty($model->photo_out)) {
                Storage::disk('public')->delete(str_replace('storage/', '', $model->photo_out));
            }
        });
    }
    public function logMails()
    {
        return $this->hasMany(LogMail::class, 'attendence_id');
    }
}
