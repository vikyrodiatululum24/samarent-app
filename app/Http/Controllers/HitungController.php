<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Helpers\PayrollHelpers;
use App\Models\DriverAttendence;
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
            'absen_id' => 'required|exists:absens,id',
        ]);

        $absen = DriverAttendence::find($request->absen_id);
        if (!$absen) {
            return response()->json(['message' => 'Absen not found'], 404);
        }

        $dayType = PayrollHelpers::cekHari($request->tanggal);
        
        $startTime = Carbon::createFromFormat('H:i', $request->start_time);
        $endTime = Carbon::createFromFormat('H:i', $request->end_time);
        $hoursWorked = $startTime->diffInMinutes($endTime) / 60;

        // transport allowance: jika start_time kurang dari 05:00 berikan transport
        $transport = 0;
        if ($startTime->lt(Carbon::createFromFormat('H:i', '05:00'))) {
            $transportMasuk = 20000; // sesuaikan nilai transport sesuai kebutuhan
        }
        // transport allowance: jika end_time lebih dari 20:00 berikan transport
        if ($endTime->gt(Carbon::createFromFormat('H:i', '20:00'))) {
            $transportPulang = 20000; // sesuaikan nilai transport sesuai kebutuhan
        }
        $transport = ($transportMasuk ?? 0) + ($transportPulang ?? 0);

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

            $modelOpertimePay = new \App\Models\OvertimePay();
            $modelOpertimePay->driver_id = $absen->driver_id;
            $modelOpertimePay->driver_attendence_id = $absen->id;
            $modelOpertimePay->tanggal = $request->tanggal;
            $modelOpertimePay->hari = Carbon::parse($request->tanggal)->locale('id')->translatedFormat('l');
            $modelOpertimePay->shift = 'Holiday';
            $modelOpertimePay->from_time = $absen->time_in;
            $modelOpertimePay->to_time = $absen->time_out;
            $modelOpertimePay->ot_hours_time = $hoursWorked;
            $modelOpertimePay->amount_per_hour = $amount_per_hour;
            $modelOpertimePay->ot_amount = round($overtimePay);
            $modelOpertimePay->transport = $transport;

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
