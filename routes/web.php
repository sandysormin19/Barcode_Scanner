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

//Landing-------------------------------------

Route::get('/', function () {
    return redirect()->route('login');
});


//Authentication----------------------------
Route::middleware(['auth'])->group(function () {

Route::get(
    '/laporan/barang-masuk/cetak',
    [LaporanController::class, 'cetakBarangMasuk']
)->name('laporan.barang-masuk.cetak');

Route::get(
    '/laporan/barang-masuk-hari-ini',
    [LaporanController::class,'barangMasukHariIni']
)->name('laporan.barang-masuk-hari-ini');

Route::get(
    '/laporan/barang-keluar/hari-ini',
    [LaporanController::class,'barangKeluarHariIni']
)->name('laporan.barang-keluar.hari-ini');

Route::get('/laporan/barang-keluar/cetak',
    [LaporanController::class, 'cetakBarangKeluar'])
    ->name('laporan.barang-keluar.cetak');

//Dashboard--------------------
  Route::get('/dashboard',
    [DashboardController::class,'index']
)->name('dashboard');


//Profile--------------------------------
    Route::get('/profile',
        [ProfileController::class,'edit']
    )->name('profile.edit');


    Route::patch('/profile',
        [ProfileController::class,'update']
    )->name('profile.update');


    Route::delete('/profile',
        [ProfileController::class,'destroy']
    )->name('profile.destroy');


//Master Barang-------------------------

Route::get(
    '/barang/cari',
    [BarangController::class,'cari']
)->name('barang.cari');


Route::resource(
    'barang',
    BarangController::class
);


Route::post(
    '/barang/import',
    [BarangController::class,'import']
)->name('barang.import');


Route::post(
    '/barang/barcode/cetak',
    [BarangController::class,'cetakBarcode']
)->name('barang.cetak-barcode');


Route::get(
    '/barang/{barang}/barcode',
    [BarangController::class,'barcode']
)->name('barang.barcode');


//Kategori------------------------------------------
    Route::resource(
        'kategori',
        KategoriController::class
    );


//Barang Masuk---------------

Route::get(
        '/barang-masuk/scanner',
        [BarangMasukController::class,'scanner']
    )->name('barang-masuk.scanner');

    Route::resource(
        'barang-masuk',
        BarangMasukController::class
    );

//Barang Keluar---------------------------------------

    Route::prefix('barang-keluar')
        ->name('barang-keluar.')
        ->group(function () {

            //Riwayat Barang Keluar
            Route::get('/',
                [BarangKeluarController::class,'index']
            )->name('index');

            //Simpan Transaksi
            Route::post('/store',
                [BarangKeluarController::class,'store']
            )->name('store');

            //Scanner Barang Keluar
            Route::get('/scanner',
                [BarangKeluarController::class,'scanner']
            )->name('scanner');

            //Cari Barang
            Route::get('/cari',
                [BarangKeluarController::class,'cari']
            )->name('cari');

            //Detail Transaksi
            Route::get('/{barang_keluar}',
                [BarangKeluarController::class, 'show']
                )->name('show');
        });


//Stok---------------------------------
    Route::get(
        '/stok',
        [StokBarangController::class,'index']
    )->name('stok.index');


    Route::get(
        '/stok-mutasi',
        [StokMutasiController::class,'index']
    )->name('stok-mutasi.index');


});

Route::get('/tes-route', function () {
    dd('INI PROJECT WMS YANG KU EDIT');
});


require __DIR__.'/auth.php';
