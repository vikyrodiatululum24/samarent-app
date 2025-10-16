<?php

namespace App\Helpers;

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
    public static function calculateOvertimePay($hoursWorked, $amount_per_hour, $overtimeRates)
    {
        $overtimePay = 0;
        if ($hoursWorked > 9) {
            $overtimeHours = $hoursWorked - 9; // fractional hours allowed

            // case: overtime up to 1 hour -> all overtime at overtime_1
            if ($overtimeHours === 1) {
                $overtimePay = 1 * $amount_per_hour * $overtimeRates['overtime_1'];
            }
            if ($overtimeHours > 1) {
                // more than 1 hour overtime: first 1 hour at overtime_1, remaining at overtime_2
                $firstHourPay = 1 * $amount_per_hour * $overtimeRates['overtime_1'];
                $remainingHours = $overtimeHours - 1;
                $remainingPay = $remainingHours * $amount_per_hour * $overtimeRates['overtime_2'];
                $overtimePay = $firstHourPay + $remainingPay;
            }
        }

        return round($overtimePay);
    }

    /**
     * Hitung gaji lembur di hari Holiday (Weekend atau Libur Nasional).
     */
    public static function calculateHolidayOvertimePay($hoursWorked, $amount_per_hour, $overtimeRates)
    {
        $overtimePay = 0;
        if ($hoursWorked > 0) {
            // All hours worked on Holiday are considered overtime
            if ($hoursWorked <= 8) {
                $overtimePay = $hoursWorked * $amount_per_hour * $overtimeRates['overtime_2'];
            }

            if ($hoursWorked > 8 && $hoursWorked <= 9) {
                $overtimeHours = 8 * $amount_per_hour * $overtimeRates['overtime_2'];
                $overtimetwoHours = ($hoursWorked - 8);
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

        return round($overtimePay);
    }
}
