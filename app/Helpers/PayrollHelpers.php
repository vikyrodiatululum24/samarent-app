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

            https: //api-harilibur.vercel.app/api/?year=2024

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
        if (!$driver_id) {
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
                // Jam lembur dihitung mulai dari jam ke-10
                $ot_1x = 1 * $overtimeRates['overtime_1'];
                // Jam ke-11 dst → rate overtime_2
                $ot_2x = ($overtimeHours - 1) * $overtimeRates['overtime_2'];
                // Total gaji lembur dalam rupiah
                $totalOvertime = $ot_1x + $ot_2x;
                $overtimePay = round($totalOvertime * $amount_per_hour, 2);
            }
        }

        if ($shift === 'Holiday' && $hoursWorked > 0) {
            $overtimeHours = $hoursWorked;
            // All hours worked on Holiday are considered overtime
            if ($hoursWorked <= 8) {
                // Jam 1–8 → rate overtime_2
                $ot_2x = $hoursWorked * $overtimeRates['overtime_2'];
                // Total gaji lembur dalam rupiah
                $totalOvertime = $ot_2x;
                $overtimePay = round($totalOvertime * $amount_per_hour, 2);
            }

            if ($hoursWorked > 8 && $hoursWorked <= 9) {
                // Jam 1–8 → rate overtime_2
                $ot_2x = 8 * $overtimeRates['overtime_2'];

                // Jam ke-9 → rate overtime_3
                $ot_3x = ($hoursWorked - 8) * $overtimeRates['overtime_3'];

                // Total semua
                $totalOvertime = $ot_2x + $ot_3x;

                // Total gaji lembur dalam rupiah
                $overtimePay = round($totalOvertime * $amount_per_hour, 2);
            }

            if ($hoursWorked > 9) {
                // Jam 1–8 → rate overtime_2
                $ot_2x = 8 * $overtimeRates['overtime_2'];

                // Jam ke-9 → rate overtime_3
                $ot_3x = 1 * $overtimeRates['overtime_3'];

                // Jam ke-10 dst → rate overtime_4
                $extraHours = $hoursWorked - 9;
                $ot_4x = $extraHours * $overtimeRates['overtime_4'];

                // Total semua
                $totalOvertime = $ot_2x + $ot_3x + $ot_4x;

                // Total gaji lembur dalam rupiah
                $overtimePay = round($totalOvertime * $amount_per_hour, 2);
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
