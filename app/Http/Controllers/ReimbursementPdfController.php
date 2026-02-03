<?php

namespace App\Http\Controllers;

use App\Models\Reimbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReimbursementPdfController extends Controller
{
    public function print(Request $request)
    {
        $query = Reimbursement::where('user_id', Auth::id());

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
}
