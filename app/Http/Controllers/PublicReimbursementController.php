<?php

namespace App\Http\Controllers;

use App\Models\Reimbursement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PublicReimbursementController extends Controller
{
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
                $fotoOdometerAwalPath = $this->compressAndStoreImage(
                    $request->file('foto_odometer_awal'),
                    'reimbursement/odometer-awal'
                );
            }

            if ($request->hasFile('foto_odometer_akhir')) {
                $fotoOdometerAkhirPath = $this->compressAndStoreImage(
                    $request->file('foto_odometer_akhir'),
                    'reimbursement/odometer-akhir'
                );
            }

            if ($request->hasFile('nota')) {
                $notaPath = $this->compressAndStoreImage(
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

    /**
     * Compress and store image
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return string
     */
    private function compressAndStoreImage($file, $directory)
    {
        // Generate unique filename
        $filename = Str::uuid() . '.jpg';
        $path = $directory . '/' . $filename;

        // Get original image
        $imageData = file_get_contents($file->getRealPath());
        $image = imagecreatefromstring($imageData);

        if ($image === false) {
            throw new \Exception('Failed to create image from uploaded file');
        }

        // Get original dimensions
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        // Set max width/height (compress jika lebih besar)
        $maxWidth = 1920;
        $maxHeight = 1920;

        // Calculate new dimensions
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);

        // Only resize if image is larger than max dimensions
        if ($ratio < 1) {
            $newWidth = (int)($originalWidth * $ratio);
            $newHeight = (int)($originalHeight * $ratio);
        } else {
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
        }

        // Create new image with calculated dimensions
        $compressedImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG
        imagealphablending($compressedImage, false);
        imagesavealpha($compressedImage, true);

        // Resize image
        imagecopyresampled(
            $compressedImage,
            $image,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $originalWidth,
            $originalHeight
        );

        // Save compressed image to temporary file
        $tempPath = sys_get_temp_dir() . '/' . $filename;
        imagejpeg($compressedImage, $tempPath, 80); // Quality 80 (0-100)

        // Store to storage
        $stored = Storage::disk('public')->put($path, file_get_contents($tempPath));

        // Clean up
        imagedestroy($image);
        imagedestroy($compressedImage);
        unlink($tempPath);

        if (!$stored) {
            throw new \Exception('Failed to store compressed image');
        }

        return $path;
    }
}
