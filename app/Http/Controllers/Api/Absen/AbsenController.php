<?php

namespace App\Http\Controllers\Api\Absen;

use App\Helpers\PayrollHelpers;
use App\Http\Controllers\Controller;
use App\Mail\AbsenConfirmationMail;
use App\Models\Driver;
use App\Models\DriverAttendence;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AbsenController extends Controller
{
    protected $fonteService;

    public function __construct()
    {
        $this->fonteService = app(FonnteService::class);
    }

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
        $driver = Driver::where('user_id', $request->user_id)->first();

        $absen = DriverAttendence::create([
            'user_id' => $request->user_id,
            'driver_id' => $driver ? $driver->id : null,
            'project_id' => $request->project_id,
            'end_user_id' => $request->end_user_id,
            'unit_id' => $request->unit_id,
            'date' => now()->format('Y-m-d'),
            'time_in' => now(),
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

        $auth = auth()->user() ? auth()->user()->id : null;

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

        $absen = DriverAttendence::where('user_id', $auth)->whereNull('time_out')->latest()->first();

        if (!$absen) {
            // Jika data absen tidak ditemukan, kembalikan respon dengan pesan error
            return response()->json(['message' => 'Data absen aktif tidak ditemukan untuk pengguna ini'], 404);
        }

        $absenCheck = $absen->checks()->create([
            'location' => $request->location_check,
            'photo' => $request->photo_check,
        ]);

        return response()->json(['message' => 'Absen check recorded successfully', 'data' => $absenCheck], 200);
    }

    public function absenKeluar(Request $request, $id)
    {
        $request->validate([
            'end_km' => 'required|numeric',
            'location_out' => 'required|string',
            'photo_out' => 'required|string',
            'send_wa' => 'sometimes|boolean',
            'send_email' => 'sometimes|boolean',
        ]);

        $absen = DriverAttendence::with(['endUser', 'user.driver'])
            ->where('id', $id)
            ->whereNull('time_out')
            ->latest()
            ->first();


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
            'time_out' => now(),
            'location_out' => $request->location_out,
            'photo_out' => $request->photo_out,
            'note' => $request->note ?? null,
        ]);

        if ($request->boolean('send_wa') || $request->boolean('send_email')) {
            $this->sendNotification(new Request([
                'ids' => [$absen->id],
                'send_wa' => $request->boolean('send_wa'),
                'send_email' => $request->boolean('send_email'),
            ]));
        }

        return response()->json(['message' => 'Absen keluar berhasil disubmit', 'data' => $absen], 200);
    }

    public function absenHistory(Request $request)
    {
        $userId = $request->user()->id;
        $history = DriverAttendence::with('confirmation:confirmable_id,status', 'checks')->where('user_id', $userId)->orderBy('date', 'desc')->limit(5)->get();

        return response()->json(['data' => $history], 200);
    }

    public function mountHistory(Request $request)
    {
        $userId = $request->user()->id;
        $month = $request->input('month', now()->format('m'));
        $year = $request->input('year', now()->format('Y'));

        $history = DriverAttendence::with('confirmation:confirmable_id,status', 'checks')->where('user_id', $userId)->whereMonth('date', $month)->whereYear('date', $year)->orderBy('date', 'desc')->get();

        return response()->json(['data' => $history], 200);
    }

    public function absenDetail(Request $request, $id)
    {
        $userId = $request->user()->id;
        $absen = DriverAttendence::with(['user.driver', 'unit', 'project', 'endUser', 'checks', 'confirmation:confirmable_id,status'])
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

        $absen = DriverAttendence::where('user_id', $userId)->whereNull('time_out')->latest()->first();

        if (!$absen) {
            return response()->json(['data' => null], 200);
        }

        return response()->json(['data' => $absen], 200);
    }

    public function sendNotification(Request $request)
    {
        $ids = $request->input('ids', []);

        $absens = DriverAttendence::with(['endUser', 'user.driver', 'confirmation'])->whereIn('id', $ids)->get();

        // generate token for each absen and create confirmation if not exists
        foreach ($absens as $absen) {
            if (!$absen->confirmation) {
                $absen->confirmation()->create([
                    'token' => bin2hex(random_bytes(16)),
                    'status' => 'pending',
                ]);
            }
        }

        $targetWa = $absens->first() && $absens->first()->endUser ? $absens->first()->endUser->no_wa : null;

        // format wa menjadi +62 jika belum ada
        if ($targetWa && !str_starts_with($targetWa, '+')) {
            $targetWa = '+62' . ltrim($targetWa, '0');
        }

        $targetEmail = 'vickyrodiatululum24@gmail.com';

        // buat url untuk membungkus id absen yang akan dikonfirmasi
        $baseUrl = 'driver.servicesamarent.com/confirm-multiple';
        $query = http_build_query(['ids' => implode(',', $ids)]);
        $url = $baseUrl . '?' . $query;
        $waUrl = 'https://wa.me/' . $targetWa . '?text=' . urlencode('Anda memiliki absen yang perlu dikonfirmasi. Silakan klik link berikut untuk melihat detail dan melakukan konfirmasi: ' . $url . ' (Jika Anda tidak melakukan servis ini, silakan abaikan pesan ini)' . "\n" . 'You have an attendance that needs confirmation. Please click the following link to view details and confirm: ' . $url . ' (If you did not perform this service, please ignore this message)');

        // if ($sendWa) {
        //     if ($targetWa) {
        //         try {
        //             $this->fonteService->sendWhatsAppMessage($targetWa, $message, null);
        //             Log::info('Notifikasi WhatsApp berhasil dikirim ke ' . $targetWa . ' untuk absen dengan ID: ' . implode(', ', $ids) . ' dengan url konfirmasi: ' . $url);
        //             $sentChannels[] = 'WhatsApp';
        //         } catch (\Exception $e) {
        //             Log::error('Gagal mengirim notifikasi WhatsApp: ' . $e->getMessage());
        //         }
        //     } else {
        //         Log::warning('Nomor WhatsApp end user tidak tersedia untuk mengirim notifikasi untuk absen dengan ID: ' . implode(', ', $ids));
        //     }
        // }

        if ($targetEmail) {
            try {
                Mail::to($targetEmail)->send(new AbsenConfirmationMail($waUrl));
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi email: ' . $e->getMessage());
            }
        } else {
            Log::warning('Email end user tidak tersedia untuk mengirim notifikasi untuk absen dengan ID: ' . implode(', ', $ids));
        }

        return response()->json([
            'message' => 'Notifikasi berhasil dikirim melalui ' . ($targetEmail ? 'email' : 'tidak ada channel yang valid'),
        ], 200);
    }

    public function detailConfirmation(Request $request)
    {
        $ids = explode(',', $request->input('ids'));

        $absens = DriverAttendence::with(['user.driver', 'unit', 'project', 'endUser', 'confirmation:confirmable_id,status,token', 'checks'])
            ->whereIn('id', $ids)
            ->get();

        if ($absens->isEmpty()) {
            return response()->json(['message' => 'Absen not found'], 404);
        }

        return response()->json(['data' => $absens], 200);
    }

    public function confirmAbsen(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'status' => 'required|in:approved,rejected',
            'is_confirmed' => 'sometimes|boolean',
        ]);

        $confirmation = \App\Models\Confirmation::where('token', $request->token)->first();

        if (!$confirmation) {
            return response()->json(['message' => 'Invalid token'], 404);
        }

        $driverAttendence = $confirmation->confirmable;
        if (!$driverAttendence || !$driverAttendence instanceof DriverAttendence) {
            return response()->json(['message' => 'Associated absen not found'], 404);
        }

        if (!empty($driverAttendence) && $request->status === 'approved') {
            PayrollHelpers::calculateOvertimePay($driverAttendence);

            $driverAttendence->update([
                'is_complete' => true,
            ]);

            $confirmation->update([
                'status' => 'approved',
                'is_confirmed' => $request->boolean('is_confirmed', true),
                'used_at' => now(),
            ]);
        } else {
            $confirmation->update([
                'status' => 'rejected',
                'is_confirmed' => $request->boolean('is_confirmed', false),
                'used_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Absen confirmation updated successfully'], 200);
    }
}
