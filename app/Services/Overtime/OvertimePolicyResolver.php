<?php

namespace App\Services\Overtime;

use App\Models\DriverAttendence;
use App\Models\Driver;
use App\Models\SetSalary;
use Illuminate\Support\Carbon;

class OvertimePolicyResolver
{
    public function resolve(Driver $driver, ?Carbon $date = null): ?SetSalary
    {
        $date ??= now();

        // Prefer per-driver override or division-level via model helper
        $driverPolicy = $driver->currentSetSalary($date);
        if ($driverPolicy) {
            return $driverPolicy;
        }

        // Fallback to scoped policies (branch, project)
        return $this->resolveScopedPolicy('division_id', $driver->division_id, $date)
            ?? $this->resolveScopedPolicy('branch_id', $driver->branch_id, $date)
            ?? $this->resolveScopedPolicy('project_id', $driver->project_id, $date);
    }

    public function resolveFromAttendance(DriverAttendence $attendance, ?Carbon $date = null): ?SetSalary
    {
        $date ??= $this->attendanceDate($attendance);
        $attendance->loadMissing(['driver']);

        $driver = $attendance->driver instanceof Driver
            ? $attendance->driver
            : Driver::query()->find($attendance->driver_id);

        return $driver ? $this->resolve($driver, $date) : null;
    }

    private function resolveScopedPolicy(string $column, ?int $value, Carbon $date): ?SetSalary
    {
        if (! $value) {
            return null;
        }

        return SetSalary::query()
            ->active()
            ->where($column, $value)
            ->where(function ($query) use ($date): void {
                $query->whereNull('effective_date')
                    ->orWhereDate('effective_date', '<=', $date->toDateString());
            })
            ->where(function ($query) use ($date): void {
                $query->whereNull('expired_date')
                    ->orWhereDate('expired_date', '>=', $date->toDateString());
            })
            ->orderByDesc('effective_date')
            ->first();
    }

    private function attendanceDate(DriverAttendence $attendance): Carbon
    {
        return Carbon::parse($attendance->date ?? $attendance->created_at ?? now())->startOfDay();
    }
}
