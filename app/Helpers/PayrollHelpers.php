<?php

namespace App\Helpers;

use App\Models\OvertimePay;
use App\Models\SetSalary;
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

            $cacheKey = 'holidays_' . $tanggal->year;

            $holidays = Cache::remember(
                $cacheKey,
                now()->addDays(30),
                function () use ($tanggal) {

                    $response = Http::timeout(5)->get(
                        "https://libur.deno.dev/api?tahun={$tanggal->year}"
                    );

                    if ($response->successful()) {
                        return collect(
                            json_decode($response->body(), true)
                        );
                    }

                    return collect();
                }
            );

            Log::info('data holidays', $holidays->toArray());

            $isHoliday = $holidays->firstWhere(
                'date',
                $tanggal->toDateString()
            );

            Log::info('Is holiday for ' . $tanggal->toDateString(), ['is_holiday' => !!$isHoliday]);

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
        $startTime = Carbon::parse($absen->time_in);
        $endTime = Carbon::parse($absen->time_out);

        Log::info('Calculating overtime pay', [
            'absen_id' => $absen->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        $driver_id = $absen->user->driver->id ?? null;
        if (!$driver_id) {
            throw new \Exception("Driver ID tidak ditemukan untuk absen ID {$absen->id}");
        }
        $tanggal = $absen->date;
        if ($absen->shift) {
            $shift = $absen->shift;
        } else {
            $shift = self::cekHari($tanggal, $absen->project_id);
            $absen->shift = $shift;
            $absen->save();
        }
        $project_id = $absen->project_id;
        $hoursWorked = $startTime->diffInMinutes($endTime) / 60;
        Log::info('Hours worked calculated', ['hours_worked' => $hoursWorked]);

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
        $workingDays = $setSalary->workhours ?? 0; // jam kerja normal per hari

        if ($shift === 'Weekday' && $hoursWorked > $workingDays) {
            $overtimeHours = $hoursWorked - $workingDays;
            Log::info('Overtime hours calculated', ['overtime_hours' => $overtimeHours, 'from ' => ['hours_worked' => $hoursWorked, 'working_days' => $workingDays]]);
            if ($overtimeHours >= 1) {
                // Jam lembur pertama → rate overtime_1
                $ot_1x = 1 * $overtimeRates['overtime_1'];
                // Jam lembur kedua dan seterusnya → rate overtime_2
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
                'ot_hours_time' => self::formatOvertimeHours($overtimeHours),
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

        Log::info('Overtime pay record created/updated', ['result' => $result]);

        return $result;
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
