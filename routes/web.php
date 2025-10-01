<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\AuthVueController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Vue\VueController;


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
        }else if ($user->role === 'driver') {
            return redirect('/absensi');
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
});

// Route::prefix('vue')->middleware('auth')->group(function () {
//     Route::get('/dashboard', [VueController::class, 'dashboard'])->name('vue.dashboard');
//     Route::get('/settings', [VueController::class, 'settings'])->name('vue.settings');
//     // Tambahkan route lain sesuai kebutuhan
//     Route::get('/user', [VueController::class, 'getUser'])->name('vue.getUser');
// });

// Route::prefix('vue')->group(function () {
//     Route::post('/register', [AuthVueController::class, 'register']);
//     Route::post('/login', [AuthVueController::class, 'login']);
//     Route::post('/logout', [AuthVueController::class, 'logout']);
// });



require __DIR__ . '/auth.php';
