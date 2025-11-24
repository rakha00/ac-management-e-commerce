<?php

use App\Filament\Pages\AmbilFotoBukti;
use App\Filament\Pages\ScanAbsensi;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\PdfController;
use App\Livewire\Cart;
use App\Livewire\Products;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

$publicRoutes = function () {
    Route::get('/', [FrontendController::class, 'home'])->name('home');
    Route::get('/servis', [FrontendController::class, 'services'])->name('services');

    // Product Routes
    Route::get('/produk', Products::class)->name('products');
    Route::get('/produk/{id}', [FrontendController::class, 'detailProducts'])->name('detail-products');
    Route::get('/produk/sparepart/{id}', [FrontendController::class, 'detailSparepart'])->name('detail-sparepart');

    // Cart Routes
    Route::get('/cart', Cart::class)->name('cart');
};

// Default Routes (Ecommerce)
Route::group([], $publicRoutes);

// Retail Routes
Route::prefix('retail')->name('retail.')->group($publicRoutes);

// Dealer Routes
Route::prefix('dealer')->name('dealer.')->group($publicRoutes);

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Absensi Routes
    Route::get('/scan-absensi', fn() => redirect(ScanAbsensi::getUrl()))->name('absensi.scan.page');

    Route::get('/absensi/foto-bukti', function () {
        $token = request()->query('token');

        return redirect($token ? AmbilFotoBukti::getUrl(['token' => $token]) : AmbilFotoBukti::getUrl());
    })->name('absensi.foto-bukti');

    Route::post('/absensi/validate-token', [AbsensiController::class, 'validateToken'])->name('absensi.validate.token');
    Route::post('/absensi/submit-with-photo', [AbsensiController::class, 'storeWithPhoto'])->name('absensi.submit.with.photo');

    // PDF / Invoice / Surat Jalan Routes
    Route::controller(PdfController::class)->group(function () {
        Route::get('/transaksi-produk/{record}/invoice/{format_type?}', 'generateTransaksiProdukInvoice')->name('transaksi-produk.invoice');
        Route::get('/transaksi-produk/{record}/surat-jalan/{format_type?}', 'generateTransaksiProdukSuratJalan')->name('transaksi-produk.surat-jalan');
        Route::get('/sparepart-keluar/{record}/invoice/{format_type?}', 'generateSparepartKeluarInvoice')->name('sparepart-keluar.invoice');
        Route::get('/transaksi-jasa/{record}/invoice/{format_type?}', 'generateTransaksiJasaInvoice')->name('transaksi-jasa.invoice');
        Route::get('/transaksi-jasa/{record}/surat-jalan/{format_type?}', 'generateTransaksiJasaSuratJalan')->name('transaksi-jasa.surat-jalan');
    });

    // Private Storage Route
    Route::get('/storage/private/{path}', function ($path) {
        $fullPath = storage_path('app/private/' . $path);
        if (!file_exists($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath);
    })->where('path', '.*')->name('storage.private');
});
