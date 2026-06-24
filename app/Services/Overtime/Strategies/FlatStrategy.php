<?php

namespace App\Services\Overtime\Strategies;

use App\Models\DriverAttendence;
use App\Models\SetSalary;
use App\Services\Overtime\Strategies\OvertimeStrategyInterface;

class FlatStrategy implements OvertimeStrategyInterface
{
    public function calculate(DriverAttendence $attendance, SetSalary $policy, array $context): array
    {
        $rate = (float) ($context['hourly_salary'] ?? 0);

        $overtimeHours = (float) ($context['overtime_hours'] ?? 0);
        $overtimePay = round($overtimeHours, 2) * $rate;

        return [
            'rate' => $rate,
            'overtime_pay' => round($overtimePay, 2),
            'overtime_hours' => round($overtimeHours, 2),
            'calculation_detail' => [
                'strategy' => 'flat',
                'rate_type' => $context['is_holiday'] ? 'holiday_rate' : 'weekday_rate',
                'rate' => $rate,
            ],
        ];
    }
}
