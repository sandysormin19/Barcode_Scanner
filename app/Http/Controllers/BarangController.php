<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarangImportRequest;
use App\Http\Requests\BarcodeRequest;
use App\Models\Barang;
use App\Models\BarangMasukSerial;
use App\Services\BarangService;
use App\Models\BarangMasukDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RealRashid\SweetAlert\Facades\Alert;

class BarangController extends Controller
{
    public function __construct(
        protected BarangService $barangService
    ) {}

    public function index(Request $request): View
{
    $perPage = (int) $request->query('per_page', 25);
    $search = trim((string) $request->query('search', ''));

    $barangs = $this->barangService->getPaginatedBarang($perPage, $search);

    return view('barang.index', compact('barangs', 'search'));
}

    public function import(BarangImportRequest $request): RedirectResponse
    {
        $this->barangService->importFromExcel($request->file('file'));

        Alert::success('Berhasil', 'Data Barang berhasil diimport.');

        return redirect()->route('barang.index');
    }

    public function cari(Request $request): JsonResponse
    {
        $barcode = trim((string) $request->input('kode_barang', ''));

        if ($barcode === '' || substr_count($barcode, '|') !== 1) {
            return response()->json([
                'status' => false,
                'message' => 'Format barcode harus Item Barang|Nomor Seri.',
            ], 422);
        }

        [$itemBarang, $serialNumber] = array_map('trim', explode('|', $barcode, 2));

        if ($itemBarang === '' || $serialNumber === '') {
            return response()->json([
                'status' => false,
                'message' => 'Format barcode harus Item Barang|Nomor Seri.',
            ], 422);
        }

        $barang = Barang::cariDenganItemBarang($itemBarang);

        if ($barang === null) {
            return response()->json([
                'status' => false,
                'message' => 'Item barang tidak ditemukan.',
            ], 404);
        }

        // Seri yang sudah dikenal memakai batch tersimpan; seri baru tetap boleh masuk.
        $serial = BarangMasukSerial::query()
            ->with('detail.barang')
            ->where('serial_number', $serialNumber)
            ->whereHas('detail.barang', function ($query) use ($itemBarang): void {
                $query->where('item_barang', $itemBarang)
                    ->where('is_active', true);
            })
            ->orderByDesc('id')
            ->first();

        $detailLama = null;

        if ($serial === null) {
            $detailLama = BarangMasukDetail::query()
                ->with('barang')
                ->where('serial_number', $serialNumber)
                ->whereHas('barang', function ($query) use ($itemBarang): void {
                    $query->where('item_barang', $itemBarang)
                        ->where('is_active', true);
                })
                ->orderByDesc('id')
                ->first();
        }

        $barang = $serial?->detail?->barang ?? $detailLama?->barang ?? $barang;

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $barang->id,
                'item_barang' => $barang->item_barang,
                'nama_barang' => $barang->nama_barang,
                'unit' => $barang->unit,
                'pakai_no_seri' => (bool) $barang->pakai_no_seri,
                'serial_number' => $serial?->serial_number ?? $detailLama?->serial_number ?? $serialNumber,
                'batch' => $serial?->batch ?? $detailLama?->batch,
            ],
        ]);
    }
    public function barcode(Barang $barang): View
    {
        $labels = $this->barangService->generateBarcodeSet([$barang->id], [$barang->id => 1]);

        return view('barang.cetak-barcode', compact('labels'));
    }

    public function cetakBarcode(BarcodeRequest $request): View
    {
        $labels = $this->barangService->generateBarcodeSet(
            $request->input('barang_id', []),
            $request->input('jumlah_cetak', [])
        );
        return view('barang.cetak-barcode', compact('labels'));
    }

    public function show(Barang $barang): View
    {
        return view('barang.show', compact('barang'));
    }
}
