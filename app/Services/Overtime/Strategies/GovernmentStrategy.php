<?php

namespace App\Services\Overtime\Strategies;

use App\Models\DriverAttendence;
use App\Models\SetSalary;
use App\Services\Overtime\Strategies\OvertimeStrategyInterface;

class GovernmentStrategy implements OvertimeStrategyInterface
{
    public function calculate(DriverAttendence $attendance, SetSalary $policy, array $context): array
    {
        $hourlySalary = (float) ($context['hourly_salary'] ?? 0);
        $overtimeHours = max(0, (float) ($context['overtime_hours'] ?? 0));
        $isHoliday = (bool) ($context['is_holiday'] ?? false);

        $segments = $isHoliday
            ? data_get($policy->rules, 'overtime.holiday', [])
            : data_get($policy->rules, 'overtime.weekday', []);

        $remainingHours = $overtimeHours;
        $overtimePay = 0.0;
        $totalHours = 0.0;
        $detail = [];

        foreach ($segments as $segment) {
            $from = (float) ($segment['from'] ?? 1);
            $to = isset($segment['to']) ? (float) $segment['to'] : null;
            $multiplier = (float) ($segment['multiplier'] ?? 1);

            if ($remainingHours <= 0) {
                break;
            }

            $segmentHours = $to !== null
                ? min($remainingHours, max(0, $to - $from + 1))
                : $remainingHours;

            $calculated_hours = $multiplier * round($segmentHours, 2);
            $calculated = $hourlySalary * round($calculated_hours, 2);
            $overtimePay += round($calculated, 2);
            $totalHours += round($calculated_hours, 2);
            $remainingHours -= $segmentHours;

            $detail[] = [
                'from' => $from,
                'to' => $to,
                'multiplier' => $multiplier,
                'hours' => $totalHours,
                'amount' => $overtimePay,
            ];
        }

        return [
            'rate' => $overtimeHours > 0 ? round($overtimePay / $overtimeHours, 2) : 0,
            'overtime_pay' => round($overtimePay, 2),
            'overtime_hours' => $overtimeHours,
            'calculation_detail' => [
                'strategy' => 'government',
                'overtime_hours' => $overtimeHours,
                'total_hours' => round($totalHours, 2),
                'hourly_salary' => round($hourlySalary, 2),
                'segments' => $detail,
            ],
        ];
    }
}
