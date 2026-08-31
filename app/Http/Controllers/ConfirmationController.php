<?php

namespace App\Http\Controllers;

use App\Models\DriverAttendence;
use App\Services\ConfirmationService;
use Illuminate\Http\Request;

class ConfirmationController extends Controller
{
    protected ConfirmationService $confirmationService;

    public function __construct(ConfirmationService $confirmationService)
    {
        $this->confirmationService = $confirmationService;
    }

    /**
     * Approve attendance via API or Web.
     */
    public function approve(Request $request, DriverAttendence $attendance)
    {
        try {
            $this->confirmationService->approve($attendance);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Kehadiran berhasil disetujui.']);
            }

            return back()->with('success', 'Kehadiran berhasil disetujui.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Gagal menyetujui kehadiran.');
        }
    }

    /**
     * Reject attendance via API or Web.
     */
    public function reject(Request $request, DriverAttendence $attendance)
    {
        $request->validate([
            'note' => 'required|string|max:500',
        ]);

        try {
            $this->confirmationService->reject($attendance, $request->note);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Kehadiran berhasil ditolak.']);
            }

            return back()->with('success', 'Kehadiran berhasil ditolak.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Gagal menolak kehadiran.');
        }
    }
}
