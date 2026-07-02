<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\AbsenConfirmationMail;
use App\Models\Confirmation;
use App\Models\DriverAttendence;
use App\Models\LogMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AbsensiController extends Controller
{
    public function sendNotificationByAdmin(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);

        // 1. Ambil semua data absen
        $absens = DriverAttendence::with(['endUser:id,email,name', 'endUserOut:id,email,name',])
            ->whereIn('id', $ids)
            ->get();

        // 2. Ambil semua user unik (IN & OUT)
        $endUsers = $absens->pluck('endUserOut')
            ->filter() // 🔥 hindari null
            ->unique('id')
            ->values();

        if ($endUsers->isEmpty()) {
            return response()->json([
                'message' => 'Absen belum selesai atau tidak memiliki end user untuk notifikasi',
                'errors' => ['No end users found for the provided absen IDs'],
            ], 400);
        }

        $errorEmails = [];
        $successEmails = [];

        foreach ($endUsers as $endUser) {
            $token = Str::random(64);

            $absensForUser = $absens
                ->filter(
                    fn($a) => $a->end_user_out == $endUser->id
                )
                ->unique('id')
                ->values();

            if ($absensForUser->isEmpty()) {
                continue;
            }

            //loop absens untuk menyimpan token konfirmasi
            foreach ($absensForUser as $a) {
                // if ($a->end_user_id == $endUser->id && $a->end_user_out == $endUser->id) {
                //     $a->type = 'IN_OUT';
                // } elseif ($a->end_user_id == $endUser->id) {
                //     $a->type = 'IN';
                // } else {
                //     $a->type = 'OUT';
                // }

                Confirmation::updateOrCreate([
                    'confirmable_type' => DriverAttendence::class,
                    'confirmable_id' => $a->id,
                    'end_user_id' => $endUser->id,
                ], [
                    'token' => $token,
                    'approval_type' => $a->type,
                    'expires_at' => now()->addDays(7),
                ]);
            }

            $url = 'driver.servicesamarent.com/confirm-multiple/' . $token;

            try {
                Mail::to($endUser->email)->queue(new AbsenConfirmationMail($url));
                $successEmails[] = $endUser->email;
                foreach ($absensForUser as $a) {
                    LogMail::updateOrCreate([
                        'attendence_id' => $a->id,
                        'end_user_id' => $endUser->id,
                    ], [
                        'status' => 'success',
                        'error_message' => null,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to dispatch SendAbsensiEmailJob for end user ' . $endUser->email . ': ' . $e->getMessage());
                foreach ($absensForUser as $a) {
                    LogMail::updateOrCreate([
                        'attendence_id' => $a->id,
                        'end_user_id' => $endUser->id,
                    ], [
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                }
                $errorEmails[] = $endUser->email;
            }
        }

        $message = count($successEmails) > 0
            ? 'Notifikasi berhasil dikirim ke: ' . implode(', ', $successEmails)
            : 'Tidak ada notifikasi yang berhasil dikirim.';

        if (!empty($errorEmails)) {
            $message .= ' Gagal dikirim ke: ' . implode(', ', $errorEmails);
        }

        return response()->json([
            'message' => $message,
            'success_emails' => $successEmails,
            'error_emails' => $errorEmails,
            'total_absens' => $absens->count(),
            'total_end_users' => $endUsers->count(),
        ]);
    }
}
