<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\PrintController;


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
    // Route::get('/pengajuan/{id}/view-spk', [PrintController::class, 'viewSpk'])->name('view.spk');
    Route::get('/pengajuan/{id}/print-spk', [PrintController::class, 'printSpk'])->name('print.spk');
    Route::get('/pengajuan/{id}/print-sjp', [PrintController::class, 'printSjp'])->name('print.sjp');
    Route::get('/pengajuan/{id}/print-lampiran', [PrintController::class, 'printLampiran'])->name('print.lampiran');
    Route::get('/pengajuan/{id}/print-lampiran2', [PrintController::class, 'printLampiran2'])->name('print.lampiran2');
});

// Route::get('/logout', function () {
//     Auth::logout();
//     // session()->invalidate();
//     // session()->regenerateToken();
//     return redirect('/login');
// })->name('logout.get');

// Admin logout
// Route::post('/admin/logout', function (Request $request) {
//     Auth::logout();
//     $request->session()->invalidate();
//     $request->session()->regenerateToken();
//     return redirect('/login'); // arahkan ke halaman login utama (Breeze)
// })->name('filament.user.auth.logout');

// // User logout
// Route::post('/user/logout', function (Request $request) {
//     Auth::logout();
//     $request->session()->invalidate();
//     $request->session()->regenerateToken();
//     return redirect('/login'); // arahkan juga ke login utama
// })->name('filament.user.auth.logout');


require __DIR__.'/auth.php';
