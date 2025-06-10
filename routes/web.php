<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
    Route::get('/pengajuan/{id}/print-spk', [PrintController::class, 'printSpk'])->name('print.spk');
    Route::get('/pengajuan/{id}/preview', [PrintController::class, 'preview'])->name('preview');
});


require __DIR__ . '/auth.php';
