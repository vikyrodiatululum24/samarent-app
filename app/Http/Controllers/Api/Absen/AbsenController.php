<?php

namespace App\Http\Controllers\Api\Absen;

use App\Http\Controllers\Controller;
use App\Models\DriverAttendence;
use Illuminate\Http\Request;

class AbsenController extends Controller
{
    public function absenMasuk(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'end_user_id' => 'required|exists:end_users,id',
            'unit_id' => 'required|exists:data_units,id',
            'start_km' => 'required|numeric',
            'location_in' => 'required|string',
            'photo_in' => 'required|string', // Assuming base64 string or URL
            'timestamp' => 'required|date',
        ]);

        $absen = DriverAttendence::create([
            'user_id' => $request->user_id,
            'project_id' => $request->project_id,
            'end_user_id' => $request->end_user_id,
            'unit_id' => $request->unit_id,
            'date' => date('Y-m-d', strtotime($request->timestamp)),
            'time_in' => date('H:i:s', strtotime($request->timestamp)),
            'start_km' => $request->start_km,
            'location_in' => $request->location_in,
            'photo_in' => $request->photo_in,
            'note' => $request->note ?? null,
        ]);

        return response()->json(['message' => 'Absen masuk recorded successfully', 'data' => $absen], 201);
    }

    public function absenCheck(Request $request, $id)
    {
        $request->validate([
            'location_check' => 'required|string',
            'photo_check' => 'required|string',
        ]);

        $absen = DriverAttendence::where('id', $id)
            ->whereNull('time_check')
            ->latest()
            ->first();

        if (!$absen) {
            return response()->json(['message' => 'No active absen found for this user'], 404);
        }

        $absen->update([
            'location_check' => $request->location_check,
            'photo_check' => $request->photo_check,
            'time_check' => date('H:i:s', strtotime($request->timestamp)),
        ]);

        return response()->json(['message' => 'Absen check recorded successfully', 'data' => $absen], 200);
    }

    public function absenKeluar(Request $request, $id)
    {
        $request->validate([
            'end_km' => 'required|numeric',
            'location_out' => 'required|string',
            'photo_out' => 'required|string',
        ]);

        $absen = DriverAttendence::where('id', $id)
            ->whereNull('time_out')
            ->latest()
            ->first();

        if (!$absen) {
            return response()->json(['message' => 'No active absen found for this user'], 404);
        }

        $absen->update([
            'end_km' => $request->end_km,
            'time_out' => date('H:i:s', strtotime($request->timestamp)),
            'location_out' => $request->location_out,
            'photo_out' => $request->photo_out,
        ]);

        return response()->json(['message' => 'Absen keluar recorded successfully', 'data' => $absen], 200);
    }

    public function absenHistory(Request $request)
    {
        $userId = $request->user()->id;
        $history = DriverAttendence::where('user_id', $userId)->orderBy('date', 'desc')->get();

        return response()->json(['data' => $history], 200);
    }

    public function absenDetail(Request $request, $id)
    {
        $userId = $request->user()->id;
        $absen = DriverAttendence::where('id', $id)->where('user_id', $userId)->first();

        if (!$absen) {
            return response()->json(['message' => 'Absen not found'], 404);
        }

        return response()->json(['data' => $absen], 200);
    }

    public function absenToday(Request $request)
    {
        $userId = $request->user()->id;
        $today = date('Y-m-d');
        $absen = DriverAttendence::where('user_id', $userId)->where('date', $today)->first();

        if (!$absen) {
            return response()->json(['message' => 'No absen record for today'], 404);
        }

        return response()->json(['data' => $absen], 200);
    }
}
