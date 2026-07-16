<?php

namespace App\Http\Controllers\Api\Absen;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReimbursementResource;
use App\Models\Reimbursement;
use App\Services\CompressImage;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ReimbursementController extends Controller
{
    protected $compressImage;
    public function __construct(CompressImage $compressImage)
    {
        $this->compressImage = $compressImage;
    }

    private function reimbursementRules(bool $isUpdate = false): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'type' => 'required|in:bbm,tol,parkir,lainnya',
            'km_awal' => 'nullable|numeric|min:0',
            'foto_odometer_awal' => 'nullable|image|max:10240',
            'km_akhir' => 'nullable|numeric|min:0|gt:km_awal',
            'foto_odometer_akhir' => 'nullable|image|max:10240',
            'nota' => $isUpdate ? 'nullable|image|max:10240' : 'required|image|max:10240',
            'tujuan_perjalanan' => 'required|string|max:500',
            'keterangan' => 'nullable|string',
            'metode_pembayaran' => 'required|string|in:fleet_card,cash',
            'dana_masuk' => 'nullable|numeric|min:0',
            'dana_keluar' => 'nullable|numeric|min:0',
        ];
    }

    private function validateReimbursement(Request $request, bool $isUpdate = false)
    {
        return Validator::make($request->all(), $this->reimbursementRules($isUpdate));
    }

    public function index()
    {
        $user = auth()->user();
        $reimbursements = Reimbursement::where('user_id', $user->id)->latest()->get();

        return response()->json([
            'message' => 'Reimbursements retrieved successfully',
            'data' => $reimbursements
        ], 200);
    }

    public function submitReimbursement(Request $request)
    {
        $validator = $this->validateReimbursement($request);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle file uploads with compression
            $fotoOdometerAwalPath = null;
            $fotoOdometerAkhirPath = null;
            $notaPath = null;

            if ($request->hasFile('foto_odometer_awal')) {
                $fotoOdometerAwalPath = $this->compressImage->compressAndStore(
                    $request->file('foto_odometer_awal'),
                    'reimbursement/odometer-awal'
                );
            }

            if ($request->hasFile('foto_odometer_akhir')) {
                $fotoOdometerAkhirPath = $this->compressImage->compressAndStore(
                    $request->file('foto_odometer_akhir'),
                    'reimbursement/odometer-akhir'
                );
            }

            if ($request->hasFile('nota')) {
                $notaPath = $this->compressImage->compressAndStore(
                    $request->file('nota'),
                    'reimbursement/nota'
                );
            }
            $reimbursement = Reimbursement::create(array_merge([
                'user_id' => $request->user_id,
                'date' => $request->date,
                'type' => $request->type,
                'km_awal' => $request->km_awal,
                'km_akhir' => $request->km_akhir,
                'tujuan_perjalanan' => $request->tujuan_perjalanan,
                'keterangan' => $request->keterangan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'dana_masuk' => $request->dana_masuk ?? 0,
                'dana_keluar' => $request->dana_keluar ?? 0,
            ], [
                'foto_odometer_awal' => $fotoOdometerAwalPath,
                'foto_odometer_akhir' => $fotoOdometerAkhirPath,
                'nota' => $notaPath
            ]));
            return response()->json([
                'message' => 'Reimbursement submitted successfully',
                'data' => $reimbursement
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error submitting reimbursement: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to submit reimbursement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $user = auth()->user();
        $reimbursement = Reimbursement::with('user')->where('id', $id)->where('user_id', $user->id)->first();

        $reimbursement = new ReimbursementResource($reimbursement);

        if (!$reimbursement) {
            return response()->json([
                'message' => 'Reimbursement not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Reimbursement retrieved successfully',
            'data' => $reimbursement
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $reimbursement = Reimbursement::where('id', $id)->where('user_id', $user->id)->first();

        if (!$reimbursement) {
            return response()->json([
                'message' => 'Reimbursement not found'
            ], 404);
        }

        $validator = $this->validateReimbursement($request, true);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $effectiveType = $request->input('type', $reimbursement->type);

        if ($effectiveType !== 'bbm') {
            if ($reimbursement->foto_odometer_awal) {
                Storage::disk('public')->delete($reimbursement->foto_odometer_awal);
            }
            if ($reimbursement->foto_odometer_akhir) {
                Storage::disk('public')->delete($reimbursement->foto_odometer_akhir);
            }
            $reimbursement->km_awal = null;
            $reimbursement->km_akhir = null;
            $reimbursement->foto_odometer_awal = null;
            $reimbursement->foto_odometer_akhir = null;
        }

        $reimbursement->update($request->only([
            'type',
            'km_awal',
            'km_akhir',
            'tujuan_perjalanan',
            'date',
            'keterangan',
            'dana_masuk',
            'dana_keluar',
            'metode_pembayaran'
        ]));

        if ($effectiveType === 'bbm' && $request->hasFile('foto_odometer_awal')) {
            if ($reimbursement->foto_odometer_awal) {
                Storage::disk('public')->delete($reimbursement->foto_odometer_awal);
            }
            $reimbursement->foto_odometer_awal = $this->compressImage->compressAndStore(
                $request->file('foto_odometer_awal'),
                'reimbursement/odometer-awal'
            );
        }

        if ($effectiveType === 'bbm' && $request->hasFile('foto_odometer_akhir')) {
            if ($reimbursement->foto_odometer_akhir) {
                Storage::disk('public')->delete($reimbursement->foto_odometer_akhir);
            }
            $reimbursement->foto_odometer_akhir = $this->compressImage->compressAndStore(
                $request->file('foto_odometer_akhir'),
                'reimbursement/odometer-akhir'
            );
        }

        if ($request->hasFile('nota')) {
            if ($reimbursement->nota) {
                Storage::disk('public')->delete($reimbursement->nota);
            }
            $reimbursement->nota = $this->compressImage->compressAndStore(
                $request->file('nota'),
                'reimbursement/nota'
            );
        }

        $reimbursement->save();

        return response()->json([
            'message' => 'Reimbursement updated successfully',
            'data' => $reimbursement
        ], 200);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $reimbursement = Reimbursement::where('id', $id)->where('user_id', $user->id)->first();

        if (!$reimbursement) {
            return response()->json([
                'message' => 'Reimbursement not found'
            ], 404);
        }

        // Delete associated files
        if ($reimbursement->foto_odometer_awal) {
            Storage::disk('public')->delete($reimbursement->foto_odometer_awal);
        }
        if ($reimbursement->foto_odometer_akhir) {
            Storage::disk('public')->delete($reimbursement->foto_odometer_akhir);
        }
        if ($reimbursement->nota) {
            Storage::disk('public')->delete($reimbursement->nota);
        }

        $reimbursement->delete();

        return response()->json([
            'message' => 'Reimbursement deleted successfully'
        ], 200);
    }
}
