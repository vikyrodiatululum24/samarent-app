<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JualController;
use App\Http\Controllers\EndUserController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/jualunit', [JualController::class, 'getunit']);
Route::get('/filters', [JualController::class, 'filters']);
Route::get('/detail/{id}', [JualController::class, 'detail']);
Route::post('/penawar', [JualController::class, 'penawar']);



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::put('/user', [AuthController::class, 'updateProfile']);
    Route::get('/getendusers/{id}', [EndUserController::class, 'getEndUsers']);
    Route::get('/getunit', [EndUserController::class, 'unit']);
    Route::get('/getproject', [EndUserController::class, 'project']);
});
