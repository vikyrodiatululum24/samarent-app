<?php

namespace App\Http\Controllers\Api\Absen;

use App\Helpers\PayrollHelpers;
use App\Http\Controllers\Controller;
use App\Jobs\SendAbsensiEmailJob;
use App\Mail\AbsenConfirmationMail;
use App\Models\Confirmation;
use App\Models\Driver;
use App\Models\DriverAttendence;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AbsenController extends Controller
{
    protected $fonteService;

    public function __construct()
    {
        $this->fonteService = app(FonnteService::class);
    }

    public function absenMasuk(Request $request)
    {
        $auth = auth()->user() ? auth()->user()->id : null;
        $request->validate([
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
        $driver = Driver::where('user_id', $auth)->first();

        DB::beginTransaction();
        try {
            $absen = DriverAttendence::create([
                'user_id' => $auth,
                'driver_id' => $driver ? $driver->id : null,
                'project_id' => $driver && $driver->project_id ? $driver->project_id : null,
                'end_user_id' => $request->end_user_id,
                'unit_id' => $request->unit_id,
                'date' => now()->format('Y-m-d'),
                'time_in' => now(),
                'start_km' => $request->start_km,
                'location_in' => $request->location_in,
                'photo_in' => $request->photo_in ?? null,
                'note' => $request->note ?? null,
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create absen masuk: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to record absen masuk', 'error' => $e->getMessage()], 500);
        }
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
            'end_user_out' => 'nullable|exists:end_users,id',
            'send_notification' => 'sometimes|boolean',
        ]);

        $absen = DriverAttendence::with(['endUser', 'user.driver', 'endUserOut'])
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
            'end_user_out' => $request->end_user_out ?? null,
        ]);

        if ($request->boolean('send_notification')) {
            $this->sendNotification(new Request([
                'ids' => [$absen->id],
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
        $absen = DriverAttendence::with(['user.driver', 'unit', 'project', 'endUser', 'endUserOut', 'checks', 'confirmation:confirmable_id,status,end_user_id'])
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

        // 1. Ambil semua data absen
        $absens = DriverAttendence::with(['endUser:id,email,name', 'endUserOut:id,email,name',])
            ->whereIn('id', $ids)
            ->get();

        // 2. Ambil semua user unik (IN & OUT)
        $endUsers = $absens->pluck('endUser')
            ->merge($absens->pluck('endUserOut'))
            ->filter() // 🔥 hindari null
            ->unique('id')
            ->values();

        foreach ($endUsers as $endUser) {
            $token = Str::random(64);



            $absensForUser = $absens
                ->filter(
                    fn($a) =>
                    $a->end_user_id == $endUser->id ||
                        $a->end_user_out == $endUser->id
                )
                ->unique('id')
                ->values();

            if ($absensForUser->isEmpty()) {
                continue;
            }

            //loop absens untuk menyimpan token konfirmasi
            foreach ($absensForUser as $a) {
                if ($a->end_user_id == $endUser->id && $a->end_user_out == $endUser->id) {
                    $a->type = 'IN_OUT';
                } elseif ($a->end_user_id == $endUser->id) {
                    $a->type = 'IN';
                } else {
                    $a->type = 'OUT';
                }

                // Cek apakah sudah ada confirmation dengan confirmable_id dan end_user_id yang sama
                $existing = Confirmation::where('confirmable_type', DriverAttendence::class)
                    ->where('confirmable_id', $a->id)
                    ->where('end_user_id', $endUser->id)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'token' => $token,
                        'expires_at' => now()->addDays(7),
                        'end_user_id' => $endUser->id,
                        'approval_type' => $a->type,
                    ]);
                    continue;
                }

                Confirmation::create([
                    'token' => $token,
                    'confirmable_type' => DriverAttendence::class,
                    'confirmable_id' => $a->id,
                    'is_confirmed' => false,
                    'end_user_id' => $endUser->id,
                    'approval_type' => $a->type,
                    'expires_at' => now()->addDays(7),
                ]);
            }

            $url = 'driver.servicesamarent.com/confirm-multiple/' . $token;

            try {
                Mail::to($endUser->email)->send(new AbsenConfirmationMail($url));
            } catch (\Exception $e) {
                Log::error('Failed to dispatch SendAbsensiEmailJob for end user ' . $endUser->email . ': ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Notifikasi selesai diproses.',
        ], 200);
    }

    public function detailConfirmation($token)
    {
        $confirmations = Confirmation::where('token', $token)->get();

        if ($confirmations->isEmpty()) {
            return response()->json(['message' => 'Invalid token'], 404);
        }

        $absens = $confirmations->map(function ($confirmation) {
            $absen = $confirmation->confirmable;
            if ($absen) {
                return [
                    'absen_id' => $absen->id,
                    'driver' => $absen->user()->first() ? $absen->user()->first()->name : null,
                    'status' => $absen->confirmation()->first() ? $absen->confirmation()->first()->status : null,
                    'project' => $absen->project()->first()->name ?? null,
                    'unit' => $absen->unit()->first() ? $absen->unit()->first()->merk : null,
                    'merk_unit' => $absen->unit()->first() ? $absen->unit()->first()->merk : null,
                    'nopol' => $absen->unit()->first() ? $absen->unit()->first()->nopol : null,
                    'end_user' => $absen->endUser(['name', 'email'])->first() ? $absen->endUser(['name', 'email'])->first()->name : null,
                    'end_user_email' => $absen->endUser(['name', 'email'])->first() ? $absen->endUser(['name', 'email'])->first()->email : null,
                    'end_user_out' => $absen->endUserOut(['name', 'email'])->first() ? $absen->endUserOut(['name', 'email'])->first()->name : null,
                    'end_user_out_email' => $absen->endUserOut(['name', 'email'])->first() ? $absen->endUserOut(['name', 'email'])->first()->email : null,
                    'location_in' => $absen->location_in,
                    'location_out' => $absen->location_out,
                    'photo_in' => $absen->photo_in,
                    'photo_out' => $absen->photo_out,
                    'note' => $absen->note,
                    'date' => $absen->date,
                    'checks' => $absen->checks()->get(['location', 'photo', 'created_at']),
                    'start_km' => $absen->start_km,
                    'end_km' => $absen->end_km,
                    'time_in' => $absen->time_in,
                    'time_out' => $absen->time_out,
                    'status' => $confirmation->status,
                    'approval_type' => $confirmation->approval_type,
                ];
            }
            return null;
        })->filter()->values();

        return response()->json(['data' => $absens], 200);

    }

    public function confirmAbsen(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'status' => 'required|in:approved,rejected',
            'absenId' => 'sometimes|exists:driver_attendences,id',
        ]);

        $confirmation = \App\Models\Confirmation::where('token', $request->token)->where('confirmable_id', $request->absenId)->first();
        $status = $request->status === 'approved' ? 'approved' : 'rejected';

        if (!$confirmation) {
            return response()->json(['message' => 'Invalid token'], 404);
        }

        $driverAttendence = $confirmation->confirmable;
        if (!$driverAttendence || !$driverAttendence instanceof DriverAttendence) {
            return response()->json(['message' => 'Associated absen not found'], 404);
        }

        if ($request->status === 'approved') {
            $confirmation->update([
                'is_confirmed' => true,
                'status' => 'approved',
            ]);

            if ($confirmation->approval_type === 'IN_OUT') {
                $driverAttendence->update([
                    'is_approved_in' => true,
                    'is_approved_out' => true,
                ]);
            } elseif ($confirmation->approval_type === 'IN') {
                $driverAttendence->update([
                    'is_approved_in' => true,
                ]);
            } elseif ($confirmation->approval_type === 'OUT') {
                $driverAttendence->update([
                    'is_approved_out' => true,
                ]);
            }
        } else {
            $confirmation->update([
                'is_confirmed' => false,
                'status' => 'rejected',
            ]);

            if ($confirmation->approval_type === 'IN_OUT') {
                $driverAttendence->update([
                    'is_approved_in' => false,
                    'is_approved_out' => false,
                ]);
            } elseif ($confirmation->approval_type === 'IN') {
                $driverAttendence->update([
                    'is_approved_in' => false,
                ]);
            } elseif ($confirmation->approval_type === 'OUT') {
                $driverAttendence->update([
                    'is_approved_out' => false,
                ]);
            }
        }

        if ($driverAttendence->is_approved_in && $driverAttendence->is_approved_out) {

            $driverAttendence->update([
                'is_complete' => true,
            ]);

            PayrollHelpers::calculateOvertimePay($driverAttendence);
        }

        return response()->json(['message' => 'Absen confirmation updated successfully'], 200);
    }
}
