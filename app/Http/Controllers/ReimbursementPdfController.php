<?php

namespace App\Http\Controllers;

use App\Models\Reimbursement;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class ReimbursementPdfController extends Controller
{
    public function print(Request $request)
    {
        $query = Reimbursement::where('user_id', Auth::id());

        if ($request->has('ids')) {
            $ids = explode(',', $request->get('ids'));
            $query->whereIn('id', $ids);
        }

        $dari = $request->get('dari');
        $sampai = $request->get('sampai');

        if ($dari) {
            $query->whereDate('created_at', '>=', $dari);
        }

        if ($sampai) {
            $query->whereDate('created_at', '<=', $sampai);
        }

        if (!$dari && !$sampai) {
            // Jika tidak ada filter tanggal, batasi ke 30 hari terakhir
            $dari = now()->subDays(30)->toDateString();
            $sampai = now()->toDateString();
            $query->whereDate('created_at', '>=', $dari)
                  ->whereDate('created_at', '<=', $sampai);
        }

        $reimbursements = $query->orderBy('created_at', 'desc')->get();
        $user = Auth::user();
        $pdf = PDF::loadView('pdf.reimbursement', compact('reimbursements', 'user', 'dari', 'sampai'))
            ->setPaper('a4', 'portrait');

        $filename = 'Laporan_Reimbursement_' . $user->name . '_' . now()->format('YmdHis') . '.pdf';

        // Stream PDF to browser (display inline)
        return $pdf->stream($filename);
    }

    public function generatePrintUrl(Request $request)
    {
        try {
            $params = [
                'user_id' => Auth::id(),
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'ids' => $request->get('ids'),
            ];

            $signedUrl = URL::temporarySignedRoute(
                'driver.reimbursement.print-pdf',
                now()->addMinutes(5),
                $params
            );

            return response()->json([
                'success' => true,
                'url' => $signedUrl,
                'message' => 'Print URL generated successfully',
                'expires_at' => '5 minutes'
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating print URL for reimbursement PDF', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error generating print URL'
            ], 500);
        }
    }

    public function driverReimbursementPrint(Request $request)
    {
        try {
            if (!$request->hasValidSignature()) {
                abort(401, 'Unauthorized');
            }

            $userId = $request->query('user_id');

            if (!$userId) {
                abort(400, 'Missing user_id parameter');
            }

            $user = User::find($userId);

            if (!$user) {
                abort(404, 'User not found');
            }

            $dari = $request->query('start_date');
            $sampai = $request->query('end_date');

            if ($request->query('ids')) {
                $ids = explode(',', $request->query('ids'));
                $reimbursements = Reimbursement::where('user_id', $userId)
                    ->whereIn('id', $ids)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $reimbursements = Reimbursement::where('user_id', $userId)
                    ->whereBetween('created_at', [
                        $dari . ' 00:00:00',
                        $sampai . ' 23:59:59'
                    ])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            $pdf = PDF::loadView('pdf.reimbursement', compact('reimbursements', 'user', 'dari', 'sampai'))
            ->setPaper('a4', 'portrait');

            $filename = 'Laporan_Reimbursement_' . $user->name . '_' . now()->format('YmdHis') . '.pdf';

            // Stream PDF to browser (display inline)
            return $pdf->stream($filename);

        } catch (\Exception $e) {
            Log::error('Error in driver reimbursement print: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'parameters' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error generating reimbursement PDF'
            ], 500);
        }
    }
}
