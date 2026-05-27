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
        'worked_hours',
        'normal_hours',
        'calculated_ot_hours',
        'amount_per_hour',
        'ot_amount',
        'out_of_town',
        'overnight',
        'transport',
        'monthly_allowance',
        'remarks',
        'calculation_detail',
    ];

    protected $casts = [
        'calculation_detail' => 'array',
        'worked_hours' => 'decimal:2',
        'normal_hours' => 'decimal:2',
        'calculated_ot_hours' => 'decimal:2',
        'amount_per_hour' => 'decimal:2',
        'ot_amount' => 'decimal:2',
        'out_of_town' => 'decimal:2',
        'overnight' => 'decimal:2',
        'transport' => 'decimal:2',
        'monthly_allowance' => 'decimal:2',
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
