<?php

namespace App\Http\Controllers;

use App\Models\Reimbursement;
use App\Models\User;
use App\Services\CompressImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PublicReimbursementController extends Controller
{
    protected $compressImage;

    public function __construct(CompressImage $compressImage)
    {
        $this->compressImage = $compressImage;
    }
    /**
     * Display the public reimbursement form
     */
    public function create(Request $request)
    {
        // Validate token if you want extra security
        $token = $request->query('token');

        // Optional: You can validate the token here
        // For now, we'll allow access with any token or without token
        if (!$token || $token !== config('app.reimbursement_token', 'reimbursement2026')) {
            abort(403, 'Invalid access token');
        }

        // Get all users for selection
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('reimbursement.create', compact('users', 'token'));
    }

    /**
     * Store the reimbursement data
     */
    public function store(Request $request)
    {
        // Validate token
        $token = $request->input('token');
        if (!$token || $token !== config('app.reimbursement_token', 'reimbursement2026')) {
            return back()->with('error', 'Invalid access token')->withInput();
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:bbm,tol,parkir,lainnya',
            'km_awal' => 'nullable|numeric|min:0',
            'foto_odometer_awal' => 'nullable|image|max:10240',
            'km_akhir' => 'nullable|numeric|min:0|gt:km_awal',
            'foto_odometer_akhir' => 'nullable|image|max:10240',
            'nota' => 'nullable|image|max:10240',
            'tujuan_perjalanan' => 'required|string|max:500',
            'keterangan' => 'nullable|string',
            'dana_masuk' => 'nullable|numeric|min:0',
            'dana_keluar' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terdapat kesalahan pada form. Silakan periksa kembali.');
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

            // Create reimbursement
            $reimbursement = Reimbursement::create([
                'user_id' => $request->user_id,
                'type' => $request->type,
                'km_awal' => $request->km_awal,
                'foto_odometer_awal' => $fotoOdometerAwalPath,
                'km_akhir' => $request->km_akhir,
                'foto_odometer_akhir' => $fotoOdometerAkhirPath,
                'nota' => $notaPath,
                'tujuan_perjalanan' => $request->tujuan_perjalanan,
                'keterangan' => $request->keterangan,
                'dana_masuk' => $request->dana_masuk ?? 0,
                'dana_keluar' => $request->dana_keluar ?? 0,
            ]);

            return redirect()
                ->route('reimbursement.success', ['token' => $token])
                ->with('success', 'Reimbursement berhasil dibuat!');

        } catch (\Exception $e) {
            // If there's an error, delete uploaded files
            if (isset($fotoOdometerAwalPath)) {
                Storage::disk('public')->delete($fotoOdometerAwalPath);
            }
            if (isset($fotoOdometerAkhirPath)) {
                Storage::disk('public')->delete($fotoOdometerAkhirPath);
            }
            if (isset($notaPath)) {
                Storage::disk('public')->delete($notaPath);
            }
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Display success page
     */
    public function success(Request $request)
    {
        $token = $request->query('token');

        if (!$token || $token !== config('app.reimbursement_token', 'reimbursement2026')) {
            abort(403, 'Invalid access token');
        }

        return view('reimbursement.success', compact('token'));
    }
}
