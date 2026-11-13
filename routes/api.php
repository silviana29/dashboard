<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index']);
    Route::post('/users', [AdminController::class, 'storeUser']);
    Route::put('/users/{id}', [AdminController::class, 'updateUser']);
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);

    Route::post('/gedungs', [AdminController::class, 'storeGedung']);
    Route::put('/gedungs/{id}', [AdminController::class, 'updateGedung']);
    Route::delete('/gedungs/{id}', [AdminController::class, 'destroyGedung']);
    Route::get('/logs', [LogController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'role:pegawai'])->group(function () {
    Route::get('/rekap-absensi', [PegawaiController::class, 'rekapAbsensi']);
    Route::get('/rekap-absensi/export', [PegawaiController::class, 'exportRekap']);
});

Route::middleware(['auth:sanctum', 'role:siswa'])->group(function () {
    Route::post('/absen/masuk', [SiswaController::class, 'absenMasuk']);
    Route::post('/absen/keluar', [SiswaController::class, 'absenKeluar']);
    Route::get('/riwayat', [SiswaController::class, 'getRiwayat']);
});
