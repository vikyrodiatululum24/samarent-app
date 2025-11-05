<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\PushwaController;
use App\Http\Controllers\AuthVueController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Vue\VueController;
use App\Http\Controllers\Api\ConfirmController;


Route::get('/', function () {
    $user = Auth::user();

    if ($user) {
        if ($user->role === 'admin') {
            return redirect('/admin');
        } else if ($user->role === 'user') {
            return redirect('/user');
        } else if ($user->role === 'finance') {
            return redirect('/finance');
        } else if ($user->role === 'manager') {
            return redirect('/manager');
        } else if ($user->role === 'asuransi') {
            return redirect('/asuransi');
        } else if ($user->role === 'admin_driver') {
            return redirect('/absensi');
        } else if ($user->role === 'admin_jual') {
            return redirect('/penjualan');
        }
    }
    return redirect('/login');
})->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/pengajuan/{id}/print-spk', [PrintController::class, 'printSpk'])->name('print.spk');
    Route::get('/pengajuan/{id}/preview', [PrintController::class, 'preview'])->name('preview');
    Route::get('/asuransi/{id}/print-asuransi', [PrintController::class, 'printAsuransi'])->name('print.asuransi');
    Route::get('/asuransi/{id}/preview', [PrintController::class, 'previewAsuransi'])->name('preview.asuransi');
    Route::get('/laporan-keuangan-service/export-pdf', [PrintController::class, 'keuanganPdf'])->name('laporan-keuangan-service.export-pdf');
    Route::get('/laporan-jualunit/{id}', [PrintController::class, 'jualunit'])->name('laporan-jualunit');
    Route::get('/laporan/absensi/{driver_id}', [PrintController::class, 'absensi'])->name('laporan-absensi');
    Route::get('/preview/absensi/{driver_id}', [PrintController::class, 'previewAbsensi'])->name('preview-laporan-absensi');
    Route::get('/export/absensi/{driver_id}/excel', [PrintController::class, 'exportAbsensiExcel'])->name('export-absensi-excel');
    Route::get('/export/overtime/{driver_id}/excel', [PrintController::class, 'exportOvertimeExcel'])->name('export-overtime-excel');
});

require __DIR__ . '/auth.php';
