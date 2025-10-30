<?php

use App\Filament\Pages\ScanAbsensi;
use App\Http\Controllers\AbsensiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    // Alias to the ScanAbsensi page (to make the URL easy to reference from QR)
    Route::get('/scan-absensi', fn () => redirect(ScanAbsensi::getUrl()))
        ->name('absensi.scan.page');

    // Endpoint to submit the scanned token for attendance recording
    Route::post('/absensi/submit', [AbsensiController::class, 'store'])
        ->name('absensi.submit');
});
