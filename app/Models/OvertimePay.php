<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OvertimePay extends Model
{
    protected $fillable = [
        'driver_id',
        'driver_attendence_id',
        'tanggal',
        'hari',
        'shift',
        'from_time',
        'to_time',
        'ot_hours_time',
        'ot_1x',
        'ot_2x',
        'ot_3x',
        'ot_4x',
        'calculated_ot_hours',
        'amount_per_hour',
        'ot_amount',
        'out_of_town',
        'overnight',
        'transport',
        'monthly_allowance',
        'remarks',
    ];

    public function driverAttendance()
    {
        return $this->belongsTo(DriverAttendence::class, 'driver_attendence_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
