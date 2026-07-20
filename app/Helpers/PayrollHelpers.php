<?php

namespace App\Helpers;

use App\Models\SetSalary;
use App\Services\Overtime\OvertimeCalculatorService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayrollHelpers
{
    /**
     * Cek apakah tanggal adalah weekday, weekend, atau libur nasional.
     */
    public static function cekHari($tanggal, $projectId): string
    {
        $tanggal = $tanggal instanceof Carbon
            ? $tanggal
            : Carbon::parse($tanggal);

        // monday, tuesday, dst
        $hari = strtolower($tanggal->format('l'));

        $workDays = SetSalary::where('project_id', $projectId)
            ->pluck('workdays')
            ->flatten()
            ->map(fn($day) => strtolower($day))
            ->unique()
            ->toArray();

        // cek hari kerja project
        if (!in_array($hari, $workDays)) {
            return 'Holiday';
        }

        try {

            $holidays = HolidayDates::getHolidayDates($tanggal->year);

            $isHoliday = $holidays->firstWhere(
                'date',
                $tanggal->toDateString()
            );

            if ($isHoliday) {
                return 'Holiday';
            }
        } catch (\Exception $e) {

            report($e);

            return 'Weekday';
        }

        return 'Weekday';
    }

    /**
     * Hitung gaji lembur di hari Weekday.
     */
    // shift, project_id, driver_id,

    public static function calculateOvertimePay($absen)
    {
        /** @var OvertimeCalculatorService $calculator */
        $calculator = app(OvertimeCalculatorService::class);

        return $calculator->calculateAndPersist($absen);
    }

    public static function formatOvertimeHours($hours)
    {
        $overtimeSeconds = $hours * 3600;
        $hours = floor($overtimeSeconds / 3600);
        $minutes = floor(($overtimeSeconds % 3600) / 60);
        $seconds = $overtimeSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
