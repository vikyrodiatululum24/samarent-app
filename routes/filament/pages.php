<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Pages\DetailHistori;

Route::get('/detail-histori/{unit}', DetailHistori::class)
    ->name('filament.pages.detail-histori');
