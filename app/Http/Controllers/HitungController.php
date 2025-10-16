<?php

namespace App\Http\Controllers;

use App\Helpers\PayrollHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class HitungController extends Controller
{
    public function index()
    {
        return view('hitung.index');
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'tanggal' => 'required|date',
        ]);

        $tanggal = PayrollHelpers::cekHari($request->tanggal);
        $dayType = $tanggal;

        $hoursWorked = Carbon::createFromFormat('H:i', $request->start_time)
            ->diffInMinutes(Carbon::createFromFormat('H:i', $request->end_time)) / 60;

        $amount_per_hour = 31195; // Example fixed amount per hour (use this directly)
        $overtime_1 = 1.5;
        $overtime_2 = 2;
        $overtime_3 = 3;
        $overtime_4 = 4;
        $overtimePay = 0;


        if ($dayType === 'Holiday') {
            $overtimePay = PayrollHelpers::calculateHolidayOvertimePay($hoursWorked, $amount_per_hour, [
                'overtime_2' => $overtime_2,
                'overtime_3' => $overtime_3,
                'overtime_4' => $overtime_4,
            ]);

            return response()->json([
                'message' => 'Perhitungan berhasil',
                'data' => [
                    'day_type' => $dayType,
                    'hours_worked' => $hoursWorked,
                    'overtime_hours' => max(0, $hoursWorked - 9),
                    'overtime_pay' => round($overtimePay),
                ],
            ]);
        } else {
            $overtimePay = PayrollHelpers::calculateOvertimePay($hoursWorked, $amount_per_hour, [
                'overtime_1' => $overtime_1,
                'overtime_2' => $overtime_2,
            ]);
            return response()->json([
                'message' => 'Perhitungan berhasil',
                'data' => [
                    'day_type' => $dayType,
                    'hours_worked' => $hoursWorked,
                    'overtime_hours' => max(0, $hoursWorked - 9),
                    'overtime_pay' => round($overtimePay),
                ],
            ]);
        }

        // Example calculation logic
        // $startTime = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
        // $endTime = \Carbon\Carbon::createFromFormat('H:i', $request->end_time);
        // calculate worked minutes to allow fractional hours (e.g., 8.5 hours)

        // $overtimePay = 0;
        // if ($hoursWorked > 9) {
        //     $overtimeHours = $hoursWorked - 9; // fractional hours allowed

        //     // case: overtime up to 1 hour -> all overtime at overtime_1
        //     if ($overtimeHours <= 1) {
        //         $overtimePay = $overtimeHours * $hourlyRate * $overtime_1;
        //     } else {
        //         // more than 1 hour overtime: first 1 hour at overtime_1, remaining at overtime_2
        //         $firstHourPay = 1 * $hourlyRate * $overtime_1;
        //         $remainingHours = $overtimeHours - 1;
        //         $remainingPay = $remainingHours * $hourlyRate * $overtime_2;
        //         $overtimePay = $firstHourPay + $remainingPay;
        //     }
        // }

        // you can return or pass $overtimePay to the view; for now we'll return it as JSON for quick testing
        // return response()->json([
        //     'message' => 'Perhitungan berhasil',
        //     'data' => [
        //         'day_type' => $dayType,
        //         'hours_worked' => $hoursWorked,
        //         'overtime_hours' => max(0, $hoursWorked - 9),
        //         'overtime_pay' => round($overtimePay, 2),
        //     ],
        // ]);
    }
}
