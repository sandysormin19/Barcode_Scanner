<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarangImportRequest;
use App\Http\Requests\BarcodeRequest;
use App\Models\Barang;
use App\Services\BarangService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Picqer\Barcode\BarcodeGeneratorPNG;
use RealRashid\SweetAlert\Facades\Alert;

class BarangController extends Controller
{
    public function __construct(
        protected BarangService $barangService
    ) {}

    public function index(Request $request): View
    {
        $perPage = (int) $request->query('per_page', 25);

        $barangs = $this->barangService->getPaginatedBarang($perPage);

        return view('barang.index', compact('barangs'));
    }

    public function import(BarangImportRequest $request): RedirectResponse
    {
        $this->barangService->importFromExcel($request->file('file'));

        Alert::success('Berhasil', 'Data Barang berhasil diimport.');

        return redirect()->route('barang.index');
    }

    public function cari(Request $request): JsonResponse
    {
        $kodeBarang = trim((string) $request->input('kode_barang', ''));

        $barang = $this->barangService->findByKodeBarang($kodeBarang);

        if (! $barang) {
            return response()->json([
                'status' => false,
                'message' => 'Barang tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $barang->id,
                'kode_barang' => $barang->kode_barang,
                'nama_barang' => $barang->nama_barang,
                'unit' => $barang->unit,
            ],
        ]);
    }

    public function barcode(Barang $barang): View
    {
        $barcode = $this->barangService->generateBarcode($barang->kode_barang);

        return view('barang.barcode', compact('barang', 'barcode'));
    }

    public function cetakBarcode(BarcodeRequest $request): View
    {
        $barangs = $this->barangService->generateBarcodeSet($request->input('barang_id', []));

        return view('barang.cetak-barcode', compact('barangs'));
    }

    public function show(Barang $barang): View
    {
        return view('barang.show', compact('barang'));
    }
}
