<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\BarangKeluarDetail;
use App\Models\BarangMasukDetail;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf as PDF;


class LaporanService
{
    public function getBarangMasukHariIni(string $tanggal, int $perPage = 25): LengthAwarePaginator
    {
        $query = BarangMasukDetail::with([
    'barang:id,kode_barang,barang_induk,item_barang,nama_barang,unit',
    'barangMasuk:id,tanggal_masuk,nomor_transaksi,operator',
])
    ->select(['id', 'barang_masuk_id', 'barang_id', 'qty', 'batch', 'created_at'])
    ->whereDate('created_at', $tanggal)
    ->orderByDesc('id');


        return Cache::remember("laporan:masuk:{$tanggal}:page:1:per:{$perPage}", 15, fn() => $query->paginate($perPage));
    }

    public function getBarangKeluarHariIni(string $tanggal, int $perPage = 25): LengthAwarePaginator
    {
        $query = BarangKeluarDetail::with(['barang:id,kode_barang,barang_induk,item_barang,nama_barang,unit','barangKeluar:id,tanggal,nomor_transaksi'])
            ->select(['id','barang_keluar_id','barang_id','qty','batch','created_at'])
            ->whereHas('barangKeluar', fn($q) => $q->whereDate('tanggal', $tanggal))
            ->orderByDesc('id');

        return Cache::remember("laporan:keluar:{$tanggal}:page:1:per:{$perPage}", 15, fn() => $query->paginate($perPage));
    }


public function generateBarangMasukPdf(string $tanggal): string
{
    $data = $this->getBarangMasukHariIni($tanggal);

    $pdf = PDF::loadView('laporan.cetak-barang-masuk', compact('data', 'tanggal'));

    $filename = 'barang_masuk_' . $tanggal . '.pdf';
    $path = storage_path('app/public/' . $filename);

    $pdf->save($path);

    return $path;
}



public function generateBarangKeluarPdf(string $tanggal): string
{
    $data = $this->getBarangKeluarHariIni($tanggal);

    $pdf = PDF::loadView('laporan.barang-keluar-pdf', ['data' => $data, 'tanggal' => $tanggal]);

    $filename = 'barang-keluar-' . $tanggal . '.pdf';
    $filepath = storage_path('app/temp/' . $filename);

    $pdf->save($filepath);

    return $filepath;
}


//* public function dispatchExport(string $type, string $tanggal, int $userId): void
// {
//    ExportLaporanJob::dispatch(['type' => $type, 'tanggal' => $tanggal, 'user_id' => $userId]);
//  }//*
}
