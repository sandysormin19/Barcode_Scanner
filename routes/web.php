<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StokBarangController;
use App\Http\Controllers\StokMutasiController;
use App\Http\Controllers\LaporanController;


Route::redirect('/', '/login');


Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('laporan')->name('laporan.')->controller(LaporanController::class)->group(function () {
        Route::get('/barang-masuk/cetak', 'cetakBarangMasuk')->name('barang-masuk.cetak');
        Route::get('/barang-masuk-hari-ini', 'barangMasukHariIni')->name('barang-masuk-hari-ini');
        Route::get('/barang-keluar/hari-ini', 'barangKeluarHariIni')->name('barang-keluar.hari-ini');
        Route::get('/barang-keluar/cetak', 'cetakBarangKeluar')->name('barang-keluar.cetak');
    });

    Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    Route::prefix('barang')->name('barang.')->controller(BarangController::class)->group(function () {
        Route::get('/cari', 'cari')->name('cari');
        Route::post('/import', 'import')->name('import');
        Route::post('/barcode/cetak', 'cetakBarcode')->name('cetak-barcode');
        Route::get('/{barang}/barcode', 'barcode')->name('barcode');
    });

    Route::resource('barang', BarangController::class)
        ->only(['index', 'show']);

    Route::resource('kategori', KategoriController::class)
        ->only(['index', 'store']);

    Route::prefix('barang-masuk')->name('barang-masuk.')->controller(BarangMasukController::class)->group(function () {
        Route::get('/scanner', 'scanner')->name('scanner');
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{barang_masuk}', 'show')->name('show');
    });

    Route::prefix('barang-keluar')->name('barang-keluar.')->controller(BarangKeluarController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/scanner', 'scanner')->name('scanner');
        Route::get('/cek-status', 'cekStatus')->name('cek-status');
        Route::post('/', 'store')->name('store');
        Route::get('/cari', 'cari')->name('cari');
        Route::get('/{barang_keluar}', 'show')->name('show');
        Route::get('/{barang_keluar}/track', 'track')->name('track');
        Route::post('/{barang_keluar}/update-status', 'updateStatus')->name('update-status');
    });

    Route::get('/stok', [StokBarangController::class, 'index'])->name('stok.index');
    Route::get('/stok-mutasi', [StokMutasiController::class, 'index'])->name('stok-mutasi.index');

});

require __DIR__ . '/auth.php';