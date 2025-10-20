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

    public static function calculateOvertimePay($endTime, $absen)
    {
        $startTime = Carbon::createFromFormat('H:i:s', $absen->time_in);
        $endTime = Carbon::createFromFormat('H:i:s', $endTime);
        $driver_id = $absen->user->driver->id ?? null;
        if (! $driver_id) {
            throw new \Exception("Driver ID tidak ditemukan untuk absen ID {$absen->id}");
        }
        $shift = self::cekHari($absen->date);
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
            'overtime_1' => $setSalary ? $setSalary->overtime1 / 100 : 0,
            'overtime_2' => $setSalary ? $setSalary->overtime2 / 100 : 0,
            'overtime_3' => $setSalary ? $setSalary->overtime3 / 100 : 0,
            'overtime_4' => $setSalary ? $setSalary->overtime4 / 100 : 0,
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
            if ($overtimeHours > 1) {
                // more than 1 hour overtime: first 1 hour at overtime_1, remaining at overtime_2
                $firstHourPay = 1 * $amount_per_hour * $overtimeRates['overtime_1'];
                $remainingHours = $overtimeHours - 1;
                $remainingPay = $remainingHours * $amount_per_hour * $overtimeRates['overtime_2'];
                $overtimePay = $firstHourPay + $remainingPay;
            }
        }

        if ($shift === 'Holiday' && $hoursWorked > 0) {
            $overtimeHours = $hoursWorked;
            // All hours worked on Holiday are considered overtime
            if ($hoursWorked <= 8) {
                $overtimePay = $hoursWorked * $amount_per_hour * $overtimeRates['overtime_2'];
            }

            if ($hoursWorked > 8) {
                $overtimeHours = 8 * $amount_per_hour * $overtimeRates['overtime_2'];
                $overtimetwoHours = $hoursWorked - 8;
                $ninthHourPay = $overtimetwoHours * $amount_per_hour * $overtimeRates['overtime_3'];
                $overtimePay = $overtimeHours + $ninthHourPay;
            }

            if ($hoursWorked > 9) {
                $firstEightHoursPay = 8 * $amount_per_hour * $overtimeRates['overtime_2'];
                $ninthHourPay = 1 * $amount_per_hour * $overtimeRates['overtime_3'];
                $remainingHours = $hoursWorked - 9;
                $remainingPay = $remainingHours * $amount_per_hour * $overtimeRates['overtime_4'];
                $overtimePay = $firstEightHoursPay + $ninthHourPay + $remainingPay;
            }
        }

        OvertimePay::updateOrCreate(
            [
                'driver_attendence_id' => $absen->id,
            ],
            [
                'driver_id' => $driver_id,
                'tanggal' => $absen->date,
                'hari' => Carbon::parse($absen->date)->locale('id')->translatedFormat('l'),
                'shift' => $shift,
                'from_time' => $startTime->format('H:i:s'),
                'to_time' => $endTime->format('H:i:s'),
                'ot_hours_time' => $overtimeHours ? gmdate('H:i:s', (int) ($overtimeHours * 3600)) : null,
                'amount_per_hour' => $amount_per_hour,
                'ot_amount' => round($overtimePay),
                'transport' => $transport,
            ],
        );

        return round($overtimePay);
    }
}
