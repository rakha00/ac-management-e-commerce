<?php

use App\Http\Controllers\AbsensiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    // Alias ke halaman ScanAbsensi (agar URL mudah di-refer dari QR)
    Route::get('/scan-absensi', fn () => redirect(\App\Filament\Pages\ScanAbsensi::getUrl()))
        ->name('absensi.scan.page');

    // Endpoint submit token hasil scan untuk pencatatan absensi
    Route::post('/absensi/submit', [AbsensiController::class, 'store'])
        ->name('absensi.submit');
});
