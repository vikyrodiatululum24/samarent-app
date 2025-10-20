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
            'absen_id' => 'required|exists:driver_attendences,id',
        ]);

        $absen = DriverAttendence::find($request->absen_id);
        $absen->load('user.driver');
        if (!$absen) {
            return response()->json(['message' => 'Absen not found'], 404);
        }

        $endTime = now()->format('H:i:s');

        try {
            $calculation = PayrollHelpers::calculateOvertimePay($endTime, $absen);
            if (!$calculation) {
            return response()->json([
                'message' => 'Perhitungan gagal',
                'detail' => 'Hasil perhitungan tidak tersedia'
            ], 422);
            }
        } catch (\Exception $e) {
            \Log::error('Perhitungan overtime gagal: '.$e->getMessage(), ['absen_id' => $absen->id]);
            return response()->json([
            'message' => 'Perhitungan gagal',
            'error' => $e->getMessage()
            ], 500);
        }

        $absen->update([
            'time_out' => $endTime,
        ]);

        return response()->json([
            'message' => 'Perhitungan berhasil',
            'data' => $calculation,
        ]);
    }
}
