<?php

namespace App\Http\Controllers\Api\Absen;

use Illuminate\Http\Request;
use App\Models\DriverAttendence;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

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
        ]);

        if ($request->has('photo_in')) {
            // If photo_in is a base64 string, save it as a file
            if (preg_match('/^data:image\/(\w+);base64,/', $request->photo_in, $type)) {
                $data = substr($request->photo_in, strpos($request->photo_in, ',') + 1);
                $data = base64_decode($data);
                $extension = strtolower($type[1]);
                $fileName = uniqid() . '.' . $extension;
                $filePath = 'absen/photo_in/' . $fileName;
                // Store the file using Laravel's Storage facade
                Storage::disk('public')->put($filePath, $data);
                $request->merge(['photo_in' => 'storage/' . $filePath]);
            }
        }

        $absen = DriverAttendence::create([
            'user_id' => $request->user_id,
            'project_id' => $request->project_id,
            'end_user_id' => $request->end_user_id,
            'unit_id' => $request->unit_id,
            'date' => now()->format('Y-m-d'),
            'time_in' => now()->format('H:i:s'),
            'start_km' => $request->start_km,
            'location_in' => $request->location_in,
            'photo_in' => $request->photo_in ?? null,
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

        if ($request->has('photo_check')) {
            // If photo_check is a base64 string, save it as a file
            if (preg_match('/^data:image\/(\w+);base64,/', $request->photo_check, $type)) {
                $data = substr($request->photo_check, strpos($request->photo_check, ',') + 1);
                $data = base64_decode($data);
                $extension = strtolower($type[1]);
                $fileName = uniqid() . '.' . $extension;
                $filePath = 'absen/photo_check/' . $fileName;
                // Store the file using Laravel's Storage facade
                Storage::disk('public')->put($filePath, $data);
                $request->merge(['photo_check' => 'storage/' . $filePath]);
            }
        }

        $absen = DriverAttendence::where('id', $id)
            ->whereNull('time_check')
            ->latest()
            ->first();

        if (!$absen) {
            // Jika data absen tidak ditemukan, kembalikan respon dengan pesan error
            return response()->json(['message' => 'Data absen aktif tidak ditemukan untuk pengguna ini'], 404);
        }

        $absen->update([
            'location_check' => $request->location_check,
            'photo_check' => $request->photo_check,
            'time_check' => now()->format('H:i:s'),
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

        $absen = DriverAttendence::with(['endUser', 'user.driver'])->where('id', $id)
            ->whereNull('time_out')
            ->latest()
            ->first();

        $target = $absen && $absen->endUser ? $absen->endUser->no_wa : null;

        if ($request->has('photo_out')) {
            // If photo_out is a base64 string, save it as a file
            if (preg_match('/^data:image\/(\w+);base64,/', $request->photo_out, $type)) {
                $data = substr($request->photo_out, strpos($request->photo_out, ',') + 1);
                $data = base64_decode($data);
                $extension = strtolower($type[1]);
                $fileName = uniqid() . '.' . $extension;
                $filePath = 'absen/photo_out/' . $fileName;
                // Store the file using Laravel's Storage facade
                Storage::disk('public')->put($filePath, $data);
                $request->merge(['photo_out' => 'storage/' . $filePath]);
            }
        }

        if (!$absen) {
            return response()->json(['message' => 'Data absen keluar tidak ditemukan untuk pengguna ini'], 404);
        }

        $absen->update([
            'end_km' => $request->end_km,
            'time_out' => now()->format('H:i:s'),
            'location_out' => $request->location_out,
            'photo_out' => $request->photo_out,
        ]);

        $absen->confirmation()->create([
            'token' => bin2hex(random_bytes(16)),
        ]);
        $url = 'https://driver.servicesamarent.com/confirm/' . $absen->confirmation->token;

        // Jika nomor WhatsApp target tersedia, kirim notifikasi
        if ($target) {
            try {
                // Kirim pesan WhatsApp menggunakan PushWaService
                app('App\Services\PushWaService')->sendMessage(
                    $target,
                    'text',
                    "Hallo " . ($absen->endUser ? $absen->endUser->name : 'User') . ",\n\n" .
                    "Terima kasih telah menggunakan layanan kami.\n" .
                    "Driver " . ($absen->user ? $absen->user->name : 'N/A') . " telah menyelesaikan tugasnya.\n" .
                    "Informasi Driver:\n" .
                    "- Nama Driver: " . ($absen->user ? $absen->user->name : 'N/A') . "\n" .
                    "- No. HP: " . ($absen->user && $absen->user->driver ? $absen->user->driver->no_wa : 'N/A') . "\n" .
                    "- Unit: " . ($absen->unit ? $absen->unit->type : 'N/A') . "\n" .
                    "- Tanggal: " . $absen->date . "\n" .
                    "- Mulai Dari: " . $absen->time_in . "\n" .
                    "- Sampai Dengan: " . $absen->time_out . "\n" .
                    "\n\n" .
                    "Silakan klik tautan berikut untuk mengonfirmasi penyelesaian tugas:\n" .
                    $url . "\n\n" .
                    "Salam,\n" .
                    "SamaRent.com"
                );
            } catch (\Exception $e) {
                Log::error("Gagal mengirim notifikasi WhatsApp: " . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Absen keluar berhasil disubmit', 'data' => $absen], 200);
    }

    public function absenHistory(Request $request)
    {
        $userId = $request->user()->id;
        $history = DriverAttendence::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        return response()->json(['data' => $history], 200);
    }

    public function mountHistory(Request $request)
    {
        $userId = $request->user()->id;
        $month = $request->input('month', now()->format('m'));
        $year = $request->input('year', now()->format('Y'));

        $history = DriverAttendence::where('user_id', $userId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json(['data' => $history], 200);
    }

    public function absenDetail(Request $request, $id)
    {
        $userId = $request->user()->id;
        $absen = DriverAttendence::with(['user.driver', 'unit', 'project', 'endUser'])
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$absen) {
            return response()->json(['message' => 'Absen not found'], 404);
        }

        return response()->json(['data' => $absen], 200);
    }

    public function checkmasuk(Request $request)
    {
        $userId = $request->user()->id;
        $today = now()->format('Y-m-d');
        $absen = DriverAttendence::where('user_id', $userId)->where('date', $today)->first();

        if (!$absen) {
            return response()->json(['data' => null], 200);
        }

        return response()->json(['data' => $absen], 200);
    }

    // public function checkabsen(Request $request)
    // {
    //     $userId = $request->user()->id;
    //     $today = now()->format('Y-m-d');
    //     $absen = DriverAttendence::where('user_id', $userId)
    //         ->where('date', $today)
    //         ->whereNull('time_check')
    //         ->first();

    //     Log::info($absen);

    //     if (!$absen) {
    //         return response()->json(['message' => 'Data absen check tidak ditemukan untuk pengguna ini'], 404);
    //     }

    //     return response()->json(['data' => $absen], 200);
    // }

    // public function checkpulang(Request $request)
    // {
    //     $userId = $request->user()->id;
    //     $today = now()->format('Y-m-d');
    //     $absen = DriverAttendence::where('user_id', $userId)
    //         ->where('date', $today)
    //         ->whereNull('time_check')
    //         ->first();

    //     if (!$absen) {
    //         return response()->json(['message' => 'Data absen pulang tidak ditemukan untuk pengguna ini'], 404);
    //     }

    //     return response()->json(['data' => $absen], 200);
    // }
}
