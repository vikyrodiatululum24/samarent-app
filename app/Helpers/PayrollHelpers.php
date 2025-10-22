<?php

namespace App\Helpers;

use App\Models\OvertimePay;
use App\Models\SetSalary;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class PayrollHelpers
{
    /**
     * Cek apakah tanggal adalah weekday, weekend, atau libur nasional.
     */
    public static function cekHari($tanggal): string
    {
        $tanggal = $tanggal instanceof Carbon ? $tanggal : Carbon::parse($tanggal);
        $hari = $tanggal->translatedFormat('l');

        if (in_array($hari, ['Saturday', 'Sunday'])) {
            return 'Holiday';
        }

        try {
            $response = Http::timeout(5)->get('https://api-harilibur.vercel.app/api', [
                'year' => $tanggal->year,
            ]);

            https://api-harilibur.vercel.app/api/?year=2024

            if ($response->successful()) {
                $holidays = collect($response->json());
                $isHoliday = $holidays->firstWhere('holiday_date', $tanggal->toDateString());

                if ($isHoliday) {
                    return 'Holiday';
                }
            }
        } catch (\Exception $e) {
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
        $startTime = Carbon::createFromFormat('H:i:s', $absen->time_in);
        $endTime = Carbon::createFromFormat('H:i:s', $absen->time_out);
        $driver_id = $absen->user->driver->id ?? null;
        if (! $driver_id) {
            throw new \Exception("Driver ID tidak ditemukan untuk absen ID {$absen->id}");
        }
        $tanggal = $absen->date;
        $shift = self::cekHari($tanggal);
        $project_id = $absen->project_id;
        $hoursWorked = $startTime->diffInMinutes($endTime) / 60;

        $setSalary = SetSalary::whereIn('project_id', [$project_id, 33])
            ->orderByRaw('project_id != ? asc', [$project_id])
            ->first();

        if (!$setSalary) {
            throw new \Exception("Data SetSalary untuk project {$project_id} tidak ditemukan");
        }

        $amount_per_hour = $setSalary ? $setSalary->amount : 0;
        $overtimeRates = [
            'overtime_1' => $setSalary ? $setSalary->overtime1 : 0,
            'overtime_2' => $setSalary ? $setSalary->overtime2 : 0,
            'overtime_3' => $setSalary ? $setSalary->overtime3 : 0,
            'overtime_4' => $setSalary ? $setSalary->overtime4 : 0,
        ];

        // transport allowance: jika start_time kurang dari 05:00 berikan transport
        if ($startTime->lt(Carbon::createFromFormat('H:i:s', '05:00:00'))) {
            $transportMasuk = $setSalary->transport; // sesuaikan nilai transport sesuai kebutuhan
        }
        // transport allowance: jika end_time lebih dari 20:00 berikan transport
        if ($endTime->gt(Carbon::createFromFormat('H:i:s', '20:00:00'))) {
            $transportPulang = $setSalary->transport; // sesuaikan nilai transport sesuai kebutuhan
        }

        $transport = ($transportMasuk ?? 0) + ($transportPulang ?? 0);

        $overtimePay = 0;
        $overtimeHours = 0;

        if ($shift === 'Weekday' && $hoursWorked > 9) {
            $overtimeHours = $hoursWorked - 9;
            if ($overtimeHours >= 1) {
                // more than 1 hour overtime: first 1 hour at overtime_1, remaining at overtime_2
                $ot_1x = 1;
                $firstHour = 1 * $overtimeRates['overtime_1'];
                $firstHour = round($firstHour, 2);
                $remainingHours = $overtimeHours - 1;
                $ot_2x = round($remainingHours, 2);
                $remainingHours = round($remainingHours, 2);
                $totalRemainingHours = $remainingHours * $overtimeRates['overtime_2'];
                $totalRemainingHours = round($totalRemainingHours, 2);
                $totalOvertime = $firstHour + $totalRemainingHours;
                $overtimePay = $totalOvertime * $amount_per_hour;
            }
        }

        if ($shift === 'Holiday' && $hoursWorked > 0) {
            $overtimeHours = $hoursWorked;
            // All hours worked on Holiday are considered overtime
            if ($hoursWorked <= 8) {
                $ot_2x = $totalOvertime;
                $totalOvertime = $hoursWorked * $overtimeRates['overtime_2'];
                $totalOvertime = round($totalOvertime, 2);
                $overtimePay = $totalOvertime * $amount_per_hour;
            }

            if ($hoursWorked > 8 && $hoursWorked <= 9) {
                $ot_2x = 8;
                $firstEightHours = 8 * $overtimeRates['overtime_2'];
                $firstEightHours = round($firstEightHours, 2);
                $ot_3x = round($hoursWorked - 8, 2);
                $ninthHour = ($hoursWorked - 8) * $overtimeRates['overtime_3'];
                $ninthHour = round($ninthHour, 2);
                $totalOvertime = $firstEightHours + $ninthHour;
                $totalOvertime = round($totalOvertime, 2);
                $overtimePay = $totalOvertime * $amount_per_hour;
            }

            if ($hoursWorked > 9) {
                $ot_2x = 8;
                $firstEightHours = 8 * $overtimeRates['overtime_2'];
                $firstEightHours = round($firstEightHours, 2);
                $ot_3x = 1;
                $ninthHour = 1 * $overtimeRates['overtime_3'];
                $ninthHour = round($ninthHour, 2);
                $remainingHours = $hoursWorked - 9;
                $remainingHours = round($remainingHours, 2);
                $ot_4x = $remainingHours;
                $remainingHours = $remainingHours * $overtimeRates['overtime_4'];
                $totalOvertime = $firstEightHours + $ninthHour + $remainingHours;
                $totalOvertime = round($totalOvertime, 2);
                $overtimePay = $totalOvertime * $amount_per_hour;
            }
        }

        $result = OvertimePay::updateOrCreate(
            [
                'driver_attendence_id' => $absen->id,
            ],
            [
                'driver_id' => $driver_id,
                'tanggal' => $tanggal,
                'hari' => Carbon::parse($tanggal)->locale('id')->translatedFormat('l'),
                'shift' => $shift,
                'from_time' => $startTime->format('H:i:s'),
                'to_time' => $endTime->format('H:i:s'),
                'ot_hours_time' => $overtimeHours ? gmdate('H:i:s', (int) ($overtimeHours * 3600)) : null,
                'ot_1x' => $ot_1x ?? 0,
                'ot_2x' => $ot_2x ?? 0,
                'ot_3x' => $ot_3x ?? 0,
                'ot_4x' => $ot_4x ?? 0,
                'calculated_ot_hours' => $totalOvertime ?? 0,
                'amount_per_hour' => $amount_per_hour,
                'ot_amount' => round($overtimePay),
                'transport' => $transport,
            ],
        );

        return $result;
    }
}
