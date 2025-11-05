<?php

use App\Filament\Pages\ScanAbsensi;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\TransaksiProdukPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/kategori/{type}', [FrontendController::class, 'category'])->name('category');
Route::get('/produk/{id}', [FrontendController::class, 'productDetail'])->name('product.detail');
Route::get('/tentang-kami', [FrontendController::class, 'aboutUs'])->name('about.us');

Route::get('/transaksi-produk/{record}/invoice', [TransaksiProdukPdfController::class, 'generateInvoice'])->name('transaksi-produk.invoice');
Route::get('/transaksi-produk/{record}/surat-jalan', [TransaksiProdukPdfController::class, 'generateSuratJalan'])->name('transaksi-produk.surat-jalan');

Route::middleware(['auth'])->group(function () {
    // Alias to the ScanAbsensi page (to make the URL easy to reference from QR)
    Route::get('/scan-absensi', fn () => redirect(ScanAbsensi::getUrl()))
        ->name('absensi.scan.page');

    // Endpoint to submit the scanned token for attendance recording
    Route::post('/absensi/submit', [AbsensiController::class, 'store'])
        ->name('absensi.submit');
});
