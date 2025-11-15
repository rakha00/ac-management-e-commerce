<?php

use App\Filament\Pages\AmbilFotoBukti;
use App\Filament\Pages\ScanAbsensi;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\PdfController;
use App\Livewire\Products;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/produk', Products::class)->name('products');
Route::get('/servis', [FrontendController::class, 'services'])->name('services');
Route::get('/produk/{slug}', [FrontendController::class, 'detailProducts'])->name('detail-products');

Route::middleware(['auth'])->group(function () {
    Route::get('/scan-absensi', fn() => redirect(ScanAbsensi::getUrl()))->name('absensi.scan.page');

    Route::get('/absensi/foto-bukti', function () {
        $token = request()->query('token');

        return redirect($token ? AmbilFotoBukti::getUrl(['token' => $token]) : AmbilFotoBukti::getUrl());
    })->name('absensi.foto-bukti');

    Route::post('/absensi/validate-token', [AbsensiController::class, 'validateToken'])->name('absensi.validate.token');
    Route::post('/absensi/submit-with-photo', [AbsensiController::class, 'storeWithPhoto'])->name('absensi.submit.with.photo');

    Route::get('/transaksi-produk/{record}/invoice/{format_type?}', [PdfController::class, 'generateTransaksiProdukInvoice'])->name('transaksi-produk.invoice');
    Route::get('/transaksi-produk/{record}/surat-jalan/{format_type?}', [PdfController::class, 'generateTransaksiProdukSuratJalan'])->name('transaksi-produk.surat-jalan');
    Route::get('/sparepart-keluar/{record}/invoice/{format_type?}', [PdfController::class, 'generateSparepartKeluarInvoice'])->name('sparepart-keluar.invoice');
    Route::get('/transaksi-jasa/{record}/invoice/{format_type?}', [PdfController::class, 'generateTransaksiJasaInvoice'])->name('transaksi-jasa.invoice');
    Route::get('/storage/private/{path}', function ($path) {
        $fullPath = storage_path('app/private/' . $path);
        if (!file_exists($fullPath)) {
            abort(404);
        }
        return response()->file($fullPath);
    })->where('path', '.*')->name('storage.private');
    Route::get('/transaksi-jasa/{record}/surat-jalan/{format_type?}', [PdfController::class, 'generateTransaksiJasaSuratJalan'])->name('transaksi-jasa.surat-jalan');
});
