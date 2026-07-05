<?php

namespace App\Services\Overtime;

use App\Enums\OvertimePolicyType;
use App\Models\DriverAttendence;
use App\Models\OvertimePay;
use App\Models\SetSalary;
use App\Services\Overtime\OvertimePolicyResolver;
use App\Services\Overtime\Strategies\CustomStrategy;
use App\Services\Overtime\Strategies\FlatStrategy;
use App\Services\Overtime\Strategies\GovernmentStrategy;
use App\Services\Overtime\Strategies\OvertimeStrategyInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class OvertimeCalculatorService
{
    public function __construct(
        private readonly OvertimePolicyResolver $resolver,
        private readonly FlatStrategy $flatStrategy,
        private readonly GovernmentStrategy $governmentStrategy,
        private readonly CustomStrategy $customStrategy,
    ) {}

    public function calculateAndPersist(DriverAttendence $attendance): OvertimePay
    {
        $attendance->loadMissing(['driver']);
        $policy = $this->resolver->resolveFromAttendance($attendance, $this->attendanceDate($attendance));

        if (! $policy) {
            throw new \RuntimeException('Overtime policy tidak ditemukan untuk driver ID ' . $attendance->getKey());
        }

        $context = $this->buildContext($attendance, $policy);
        $result = $this->strategyFor($policy)->calculate($attendance, $policy, $context);
        if ($attendance->shift === null) {
            $isHoliday = $context['is_holiday'] ? 'Holiday' : 'Weekday';
            $attendance->shift = $isHoliday;
            $attendance->save();
        }

        return OvertimePay::query()->updateOrCreate(
            [
                'driver_attendence_id' => $attendance->getKey(),
            ],
            [
                'driver_id' => $attendance->driver_id,
                'driver_attendence_id' => $attendance->getKey(),
                'tanggal' => $context['date']->toDateString(),
                'hari' => $context['date']->locale('id')->translatedFormat('l'),
                'shift' => $attendance->shift,
                'from_time' => $context['clock_in']->format('H:i:s'),
                'to_time' => $context['clock_out']->format('H:i:s'),
                'worked_hours' => $context['worked_hours'],
                'normal_hours' => $context['normal_hours'],
                'ot_hours_time' => $this->formatOvertimeHours((float) $result['overtime_hours']),
                'calculated_ot_hours' => $result['overtime_hours'],
                'amount_per_hour' => $context['hourly_salary'],
                'ot_amount' => $result['overtime_pay'],
                'calculation_detail' => array_merge($result['calculation_detail'], [
                    'policy_id' => $policy->getKey(),
                    'policy_type' => $policy->policy_type,
                    'attendance_id' => $attendance->getKey(),
                    'workday_hours' => $context['normal_hours'],
                    'is_holiday' => $context['is_holiday'],
                ]),
            ]
        );
    }

    public function preview(DriverAttendence $attendance): array
    {
        $attendance->loadMissing(['driver']);
        $policy = $this->resolver->resolveFromAttendance($attendance, $this->attendanceDate($attendance));

        if (! $policy) {
            throw new \RuntimeException('Overtime policy tidak ditemukan untuk attendance ID ' . $attendance->getKey());
        }

        $context = $this->buildContext($attendance, $policy);

        return $this->strategyFor($policy)->calculate($attendance, $policy, $context);
    }

    private function strategyFor(SetSalary $policy): OvertimeStrategyInterface
    {
        return match ($policy->policy_type) {
            OvertimePolicyType::Government->value => $this->governmentStrategy,
            // OvertimePolicyType::Custom->value => $this->customStrategy,
            default => $this->flatStrategy,
        };
    }

    private function buildContext(DriverAttendence $attendance, SetSalary $policy): array
    {
        $date = $this->attendanceDate($attendance);
        $clockIn = $this->resolveDateTime($attendance, 'time_in');
        $clockOut = $this->resolveDateTime($attendance, 'time_out');
        $workedHours = max(0, $clockIn->diffInMinutes($clockOut) / 60);
        $workdays = $this->normalizeWorkdays((array) ($policy->workdays ?? data_get($policy->rules, 'workdays', [])));
        $day = strtolower($date->format('l'));
        $normalHours = (float) ($workdays[$day] ?? 8);
        if ($attendance->shift) {
            $isHoliday = $attendance->shift === 'Holiday';
        } else {
            $isHoliday = $this->isHoliday($date) || ! array_key_exists($day, $workdays);
        }
        $overtimeHours = max(0, $isHoliday ? $workedHours : $workedHours - $normalHours);
        $effectiveHours = $this->applyBreakRule($overtimeHours, $isHoliday, (array) ($policy->rules['break'] ?? []));

        if ($policy->policy_type === OvertimePolicyType::Government->value) {
            $salary = (float) (data_get($policy->rules['overtime'], 'hourly_salary') ?? 0);
        } else if ($policy->policy_type === OvertimePolicyType::Flat->value) {
            $salary = (float) $isHoliday
                ? (data_get($policy->rules['overtime'], 'holiday_rate') ?? 0)
                : (data_get($policy->rules['overtime'], 'weekday_rate') ?? 0);
        } else {
            $salary = (float) ($policy->rules['overtime']['hourly_salary'] ?? 0);
        }

        return [
            'date' => $date,
            'day' => $day,
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'worked_hours' => $workedHours,
            'normal_hours' => $normalHours,
            'overtime_hours' => $effectiveHours,
            'is_holiday' => $isHoliday,
            'hourly_salary' => $salary,
        ];
    }

    private function applyBreakRule(float $workedHours, bool $isHoliday, array $breakRule): float
    {
        if (! $isHoliday) {
            return $workedHours;
        }

        if (! (bool) ($breakRule['enabled'] ?? true)) {
            return $workedHours;
        }

        $afterHours = (float) ($breakRule['after_hours'] ?? 5);
        $deductHours = (float) ($breakRule['deduct_hours'] ?? 1);

        return $workedHours >= $afterHours
            ? max(0, $workedHours - $deductHours)
            : $workedHours;
    }

    private function normalizeWorkdays(array $workdays): array
    {
        if (! array_is_list($workdays)) {
            return $workdays;
        }

        $normalized = [];

        foreach ($workdays as $item) {
            $day = strtolower((string) ($item['day'] ?? ''));

            if ($day === '') {
                continue;
            }

            $normalized[$day] = (float) ($item['hours'] ?? 0);
        }

        return $normalized;
    }

    private function isHoliday(Carbon $date): bool
    {
        $holidays = Cache::remember('holiday_api_' . $date->year, now()->addDays(30), function () use ($date) {
            $response = Http::timeout(5)->get('https://libur.deno.dev/api?tahun=' . $date->year);

            if (! $response->successful()) {
                return collect();
            }

            return collect($response->json() ?? []);
        });

        return (bool) $holidays->firstWhere('date', $date->toDateString());
    }

    private function attendanceDate(DriverAttendence $attendance): Carbon
    {
        return Carbon::parse($attendance->date ?? $attendance->created_at ?? now())->startOfDay();
    }

    private function resolveDateTime(DriverAttendence $attendance, string $clockField): Carbon
    {
        $baseDate = $this->attendanceDate($attendance)->toDateString();
        $value = $attendance->{$clockField} ?? null;

        if ($value instanceof Carbon) {
            return $value;
        }

        if (blank($value)) {
            return Carbon::parse($baseDate . ' 00:00:00');
        }

        return Carbon::parse(str_contains((string) $value, ' ') ? (string) $value : $baseDate . ' ' . $value);
    }

    private function formatOvertimeHours(float $hours): string
    {
        $seconds = (int) round($hours * 3600);
        $formattedHours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $formattedHours, $minutes, $remainingSeconds);
    }
}
