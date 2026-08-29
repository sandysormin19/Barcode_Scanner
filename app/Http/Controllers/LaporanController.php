<?php

namespace App\Http\Controllers;

use App\Services\LaporanService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanController extends Controller
{
    public function __construct(
        protected LaporanService $laporanService
    ) {}

    public function barangMasukHariIni(Request $request): View
    {
        $tanggal = today()->toDateString();
        $perPage = (int) $request->query('per_page', 25);

        $data = $this->laporanService->getBarangMasukHariIni($tanggal, $perPage);

        return view('laporan.barang-masuk', compact('data', 'tanggal'));
    }

    public function barangKeluarHariIni(Request $request): View
    {
        $tanggal = today()->toDateString();
        $perPage = (int) $request->query('per_page', 25);

        $data = $this->laporanService->getBarangKeluarHariIni($tanggal, $perPage);

        return view('laporan.barang-keluar', compact('data', 'tanggal'));
    }

    public function cetakBarangMasuk(Request $request): View
    {
        $tanggal = $request->query('tanggal', today()->toDateString());

        $data = $this->laporanService->getBarangMasukHariIni($tanggal);

        return view('laporan.cetak-barang-masuk', compact('data', 'tanggal'));
    }

    public function cetakBarangKeluar(Request $request): View
    {
        $tanggal = $request->query('tanggal', today()->toDateString());

        $data = $this->laporanService->getBarangKeluarHariIni($tanggal);

        return view('laporan.cetak-barang-keluar', compact('data', 'tanggal'));
    }
}
