<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    protected static function booted()
    {
        static::saving(function (self $model) {
            if (empty($model->location)) {
                $model->location = '-';
            }
        });
        static::updating(function (self $model) {
            if ($model->isDirty('photo')) {
                $old = $model->getOriginal('photo');
                if (! empty($old)) {
                    $path = str_replace('storage/', '', $old);
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }
        });

        static::deleting(function (self $model) {
            if (! empty($model->photo)) {
                $path = str_replace('storage/', '', $model->photo);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        });
    }
}
