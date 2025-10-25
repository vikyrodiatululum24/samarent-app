<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'user_id',
        'password',
        'nik',
        'sim',
        'alamat',
        'no_wa',
        'tempat',
        'tanggal_lahir',
        'jenis_kelamin',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'agama',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // public function driverAttendences()
    // {
    //     return $this->hasManyThrough(
    //         DriverAttendence::class,
    //         User::class,
    //         'id', // foreign key di User
    //         'user_id', // foreign key di DriverAttendence
    //         'user_id', // local key di Driver
    //         'id' // local key di User
    //     );
    // }
    public function driverAttendences()
    {
        return $this->hasMany(DriverAttendence::class, 'driver_id');
    }
    public function overtimePay()
    {
        return $this->hasMany(OvertimePay::class, 'driver_id');
    }
}
