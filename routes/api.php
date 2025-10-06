<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PushwaController;
use App\Http\Controllers\Api\JualController;
use App\Http\Controllers\Api\ConfirmController;
use App\Http\Controllers\Api\Absen\AuthController;
use App\Http\Controllers\Api\Absen\AbsenController;
use App\Http\Controllers\Api\Absen\EndUserController;

// absensi
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/confirm/{token}', [ConfirmController::class, 'confirmAbsen']);

// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/reverse-geocode', function (Request $request) {
        $lat = $request->query('lat');
        $lon = $request->query('lon');

        if (!$lat || !$lon) {
            return response()->json([
                'error' => 'Parameter lat dan lon wajib diisi'
            ], 400);
        }

        $response = Http::withHeaders([
            'User-Agent' => 'LaravelApp/1.0 (vikyrodiatululum@gmail.com)'
        ])->timeout(10)->get("https://nominatim.openstreetmap.org/reverse", [
            'lat' => $lat,
            'lon' => $lon,
            'format' => 'json',
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Request ke Nominatim gagal',
                'status' => $response->status(),
                'body'   => $response->body(),
            ], 500);
        }

        return $response->json();
    });
    Route::get('/user', function (Request $request) {
        return $request->user()->load('driver');
    });
    Route::put('/user', [AuthController::class, 'updateProfile']);
    Route::get('/avatar', [AuthController::class, 'getAvatar']);
    Route::get('/getendusers/{id}', [EndUserController::class, 'getEndUsers']);
    Route::get('/getunit', [EndUserController::class, 'unit']);
    Route::get('/getproject', [EndUserController::class, 'project']);
    Route::post('/absen/masuk', [AbsenController::class, 'absenMasuk']);
    Route::put('/absen/{id}/check', [AbsenController::class, 'absenCheck']);
    Route::put('/absen/{id}/keluar', [AbsenController::class, 'absenKeluar']);
    Route::get('/absen/history', [AbsenController::class, 'absenHistory']);
    Route::get('/absen/month', [AbsenController::class, 'mountHistory']);
    Route::get('/checkmasuk', [AbsenController::class, 'checkmasuk']);
    Route::get('/absen/history/{id}', [AbsenController::class, 'absenDetail']);

    Route::post('/logout', [AuthController::class, 'logout']);
});

// jual mobil
Route::get('/jualunit', [JualController::class, 'getunit']);
Route::get('/filters', [JualController::class, 'filters']);
Route::get('/detail/{id}', [JualController::class, 'detail']);
Route::post('/penawar', [JualController::class, 'penawar']);
