<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EndUser extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'email',
        'no_wa',
    ];

    public function driverAttendences()
    {
        return $this->hasMany(DriverAttendence::class);
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
