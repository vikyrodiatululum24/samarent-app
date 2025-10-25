<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helpers\PayrollHelpers;
use App\Http\Controllers\Controller;

class ConfirmController extends Controller
{
    public function confirmAbsen($token)
    {
        $confirmation = \App\Models\Confirmation::where('token', $token)->first();

        if (!$confirmation) {
            return response()->json(['message' => 'Token tidak valid'], 400);
        }

        $confirmation->is_confirmed = true;
        $confirmation->used_at = now();
        $confirmation->save();

        // Update the related DriverAttendence record
        if ($confirmation->confirmable_type === \App\Models\DriverAttendence::class) {
            $absen = $confirmation->confirmable;
            if ($absen) {
                $absen->load('user', 'unit');
                $absen->is_complete = true;
                $absen->save();
                $driver = $absen->user ? $absen->user->name : null;
                $type_unit = $absen->unit ? $absen->unit->type : 'N/A';
                $mulai = $absen->time_in ? $absen->time_in : 'N/A';
                $selesai = $absen->time_out ? $absen->time_out : 'N/A';
            }

            try {
                $calculation = PayrollHelpers::calculateOvertimePay($absen);
                if (!$calculation) {
                    return response()->json(
                        [
                            'message' => 'Perhitungan gagal',
                            'detail' => 'Hasil perhitungan tidak tersedia',
                        ],
                        422,
                    );
                }
            } catch (\Exception $e) {
                \Log::error('Perhitungan overtime gagal: ' . $e->getMessage(), ['absen_id' => $absen->id]);
                return response()->json(
                    [
                        'message' => 'Perhitungan gagal',
                        'error' => $e->getMessage(),
                    ],
                    500,
                );
            }
        }

        return response()->json([
            'data' => [
                'message' => 'Absen berhasil dikonfirmasi',
                'date' => $absen->date,
                'driver' => $driver ?? 'N/A',
                'type_unit' => $type_unit ?? 'N/A',
                'mulai' => $mulai ?? 'N/A',
                'selesai' => $selesai ?? 'N/A',
            ],
        ]);
    }
}
