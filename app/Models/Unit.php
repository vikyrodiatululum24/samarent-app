<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'data_units';
    protected $fillable = [
        'no_rks',
        'penyerahan_unit',
        'jenis',
        'merk',
        'type',
        'nopol',
        'no_rangka',
        'no_mesin',
        'tgl_pajak',
        'regional',
    ];

    public function serviceUnit()
    {
        return $this->hasMany(ServiceUnit::class, 'unit_id', 'id');
    }

    public function asuransi()
    {
        return $this->hasMany(Asuransi::class, 'unit_id', 'id');
    }
    public function driverAttendences()
    {
        return $this->hasMany(DriverAttendence::class, 'unit_id', 'id');
    }
    public function unitJual()
    {
        return $this->hasOne(UnitJual::class, 'unit_id', 'id');
    }
}
