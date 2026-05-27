<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Division;
use Carbon\Carbon;

class Driver extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'branch_id',
        'division_id',
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
        'pic',
        'salary',
        'set_salary_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'salary' => 'decimal:2',
    ];

    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function setSalary()
    {
        return $this->belongsTo(SetSalary::class);
    }

    /**
     * Get the active SetSalary for this driver.
     * Priority: per-driver override (`set_salary_id`) then division defaults.
     * Returns null when none found.
     */
    public function currentSetSalary(?\Illuminate\Support\Carbon $date = null)
    {
        $date = $date ? $date->startOfDay() : Carbon::today();

        // Check per-driver override first
        if ($this->setSalary) {
            $ss = $this->setSalary;
            $effectiveOk = is_null($ss->effective_date) || $ss->effective_date->lte($date);
            $expiredOk = is_null($ss->expired_date) || $ss->expired_date->gte($date);
            if ($ss->is_active && $effectiveOk && $expiredOk) {
                return $ss;
            }
        }

        // Fallback to division-level SetSalaries
        if ($this->division) {
            return $this->division->setSalaries()
                ->active()
                ->where(function ($q) use ($date) {
                    $q->whereNull('effective_date')->orWhereDate('effective_date', '<=', $date->toDateString());
                })
                ->where(function ($q) use ($date) {
                    $q->whereNull('expired_date')->orWhereDate('expired_date', '>=', $date->toDateString());
                })
                ->orderByDesc('effective_date')
                ->first();
        }

        return null;
    }

    public function driverAttendences()
    {
        return $this->hasMany(DriverAttendence::class, 'driver_id');
    }
    public function overtimePay()
    {
        return $this->hasMany(OvertimePay::class, 'driver_id');
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function reimbursements()
    {
        return $this->hasMany(Reimbursement::class, 'user_id', 'user_id');
    }
}
