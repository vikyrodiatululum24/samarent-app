<?php

namespace App\Http\Controllers;

use App\Exports\ReimbursementExport;
use App\Exports\MonitoringReimbursementExport;
use App\Models\Driver;
use App\Models\GroupSignature;
use App\Models\Reimbursement;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;

class ReimbursementPdfController extends Controller
{
    public function print(Request $request)
    {
        $query = Reimbursement::where('user_id', Auth::id());

        if ($request->has('ids')) {
            $ids = explode(',', $request->get('ids'));
            $query->whereIn('id', $ids);

            $dari = $query->min('created_at');
            $sampai = $query->max('created_at');
        } else {
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
                $dari = now()->subDays(31)->toDateString();
                $sampai = now()->toDateString();
                $query->whereDate('created_at', '>=', $dari)->whereDate('created_at', '<=', $sampai);
            }
        }

        $reimbursements = $query->orderBy('created_at', 'asc')->get();
        $user = Auth::user();
        $pdf = PDF::loadView('pdf.reimbursement', compact('reimbursements', 'user', 'dari', 'sampai'))->setPaper('a4', 'portrait');

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

            $signedUrl = URL::temporarySignedRoute('driver.reimbursement.print-pdf', now()->addMinutes(5), $params);

            return response()->json([
                'success' => true,
                'url' => $signedUrl,
                'message' => 'Print URL generated successfully',
                'expires_at' => '5 minutes',
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating print URL for reimbursement PDF', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error generating print URL',
                ],
                500,
            );
        }
    }

    public function driverReimbursementPrint(Request $request)
    {
        try {
            if (!$request->hasValidSignature()) {
                abort(401, 'Unauthorized');
            }

            $userId = $request->query('user_id');

            $driver = Driver::where('user_id', $userId)->first();
            $project = $driver->project_id;
            $branch = $driver->branch_id;
            $group_signature = GroupSignature::with([
                'rule_signatures.signatures',
            ])->where('branch_id', $branch)->where('project_id', $project)->first();

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
                $reimbursements = Reimbursement::where('user_id', $userId)->whereIn('id', $ids)->orderBy('created_at', 'asc')->get();
            } elseif ($dari && $sampai) {
                $reimbursements = Reimbursement::where('user_id', $userId)->whereDate('created_at', '>=', $dari)->whereDate('created_at', '<=', $sampai)->orderBy('created_at', 'asc')->get();
            } else {
                // Jika tidak ada filter tanggal, batasi ke 31 hari terakhir
                $dari = now()->subDays(31)->toDateString();
                $sampai = now()->toDateString();
                $reimbursements = Reimbursement::where('user_id', $userId)->whereDate('created_at', '>=', $dari)->whereDate('created_at', '<=', $sampai)->orderBy('created_at', 'asc')->get();
            }

            $pdf = PDF::loadView('pdf.reimbursement', compact('reimbursements', 'user', 'dari', 'sampai', 'group_signature'))->setPaper('a4', 'portrait');

            $filename = 'Laporan_Reimbursement_' . $user->name . '_' . now()->format('YmdHis') . '.pdf';

            // Stream PDF to browser (display inline)
            return $pdf->stream($filename);
        } catch (\Exception $e) {
            Log::error('Error in driver reimbursement print: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'parameters' => $request->all(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error generating reimbursement PDF',
                ],
                500,
            );
        }
    }

    public function managerPrintReimbursement(Request $request)
    {
        try {
            $userId = $request->query('user_id');
            $driver = Driver::where('user_id', $userId)->first();
            $project = $driver->project_id;
            $branch = $driver->branch_id;
            $group_signature = GroupSignature::with([
                'rule_signatures.signatures',
            ])->where('branch_id', $branch)->where('project_id', $project)->first();

            if (!$userId) {
                abort(400, 'Missing user_id parameter');
            }

            $user = User::find($userId);

            if (!$user) {
                abort(404, 'User not found');
            }

            $dari = $request->get('dari');
            $sampai = $request->get('sampai');

            if ($request->query('ids')) {
                $ids = explode(',', $request->query('ids'));
                $reimbursements = Reimbursement::where('user_id', $userId)->whereIn('id', $ids)->orderBy('date', 'asc')->get();

                $dari = $reimbursements->min('date');
                $sampai = $reimbursements->max('date');
            } else {
                $reimbursements = Reimbursement::where('user_id', $userId)
                    ->whereDate('date', '>=', $dari)
                    ->whereDate('date', '<=', $sampai)
                    ->orderBy('date', 'asc')
                    ->get();
            }

            $pdf = PDF::loadView('pdf.reimbursement', compact('reimbursements', 'user', 'dari', 'sampai', 'group_signature'))->setPaper('a4', 'portrait');

            $filename = 'Laporan_Reimbursement_' . $user->name . '_' . now()->format('YmdHis') . '.pdf';

            // Stream PDF to browser (display inline)
            return $pdf->stream($filename);
        } catch (\Exception $e) {
            Log::error('Error in manager reimbursement print: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'parameters' => $request->all(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error generating reimbursement PDF',
                ],
                500,
            );
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $userId = Auth::user()->id;

            if (!$userId) {
                abort(400, 'Missing user_id parameter');
            }

            $user = User::find($userId);

            if (!$user) {
                abort(404, 'User not found');
            }

            if ($request->query('ids')) {
                $ids = explode(',', $request->query('ids'));
                $reimbursements = Reimbursement::where('user_id', $userId)->whereIn('id', $ids)->orderBy('date', 'asc')->get();

                $dari = $reimbursements->min('date');
                $sampai = $reimbursements->max('date');
            } else {
                $dari = $request->get('dari');
                $sampai = $request->get('sampai');
                $reimbursements = Reimbursement::where('user_id', $userId)
                    ->whereDate('date', '>=', $dari)
                    ->whereDate('date', '<=', $sampai)
                    ->orderBy('date', 'asc')
                    ->get();
            }

            $filename = 'Laporan_Reimbursement_' . $user->name . '_' . now()->format('YmdHis') . '.xlsx';

            return Excel::download(new ReimbursementExport($reimbursements, $dari, $sampai, $user), $filename);
        } catch (\Exception $e) {
            Log::error('Error in manager reimbursement export: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'parameters' => $request->all(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error generating reimbursement Excel',
                ],
                500,
            );
        }
    }

    public function MonitoringReimbursementPrint(Request $request)
    {
        try {
            $dari = $request->get('dari');
            $sampai = $request->get('sampai');

            if ($request->query('ids')) {
                $ids = explode(',', $request->query('ids'));
                $reimbursements = Reimbursement::whereIn('id', $ids)->orderBy('date', 'asc')->get();

                $dari = $reimbursements->min('date');
                $sampai = $reimbursements->max('date');
            } else {
                $reimbursements = Reimbursement::whereDate('date', '>=', $dari)
                    ->whereDate('date', '<=', $sampai)
                    ->orderBy('date', 'asc')
                    ->get();
            }

            $pdf = PDF::loadView('pdf.monitoring-reimbursement', compact('reimbursements', 'dari', 'sampai'))->setPaper('a4', 'portrait');

            $filename = 'Laporan_Monitoring_Reimbursement_' . now()->format('YmdHis') . '.pdf';

            // Stream PDF to browser (display inline)
            return $pdf->stream($filename);
        } catch (\Exception $e) {
            Log::error('Error in monitoring reimbursement print: ' . $e->getMessage(), [
                'parameters' => $request->all(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error generating monitoring reimbursement PDF',
                ],
                500,
            );
        }
    }

    public function MonitoringReimbursementExportExcel(Request $request)
    {
        try {
            $dari = $request->get('dari');
            $sampai = $request->get('sampai');

            if ($request->query('ids')) {
                $ids = explode(',', $request->query('ids'));
                $reimbursements = Reimbursement::whereIn('id', $ids)->orderBy('date', 'asc')->get();

                $dari = $reimbursements->min('date');
                $sampai = $reimbursements->max('date');
            } else {
                $reimbursements = Reimbursement::whereDate('date', '>=', $dari)
                    ->whereDate('date', '<=', $sampai)
                    ->orderBy('date', 'asc')
                    ->get();
            }

            $filename = 'Laporan_Monitoring_Reimbursement_' . now()->format('YmdHis') . '.xlsx';

            return Excel::download(new MonitoringReimbursementExport($reimbursements), $filename);
        } catch (\Exception $e) {
            Log::error('Error in monitoring reimbursement export: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'parameters' => $request->all(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error generating reimbursement Excel',
                ],
                500,
            );
        }
    }
}
