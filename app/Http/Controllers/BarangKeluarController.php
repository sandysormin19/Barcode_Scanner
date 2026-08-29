<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarangKeluarStoreRequest;
use App\Models\BarangKeluar;
use App\Services\BarangKeluarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarangKeluarController extends Controller
{
    public function __construct(
        protected BarangKeluarService $barangKeluarService)
    {}

    public function index(Request $request): View
    {
        $perPage = (int) $request->query('per_page', 10);

        $barangKeluars = $this->barangKeluarService->getPaginatedKeluar($perPage);

        return view('barang-keluar.index', compact('barangKeluars'));
    }
    public function scanner(): View
    {
        $nomor = $this->barangKeluarService->generateNomor();

        return view('barang-keluar.scanner', compact('nomor'));
    }

    public function store(BarangKeluarStoreRequest $request): RedirectResponse
    {
        $this->barangKeluarService->createKeluar($request->validated());

        return redirect()->route('barang-keluar.index')
            ->with('success', 'Barang keluar berhasil disimpan.');
    }

    public function cari(Request $request): JsonResponse
    {
        $kodeBarang = trim((string) $request->input('kode_barang', ''));

        $data = $this->barangKeluarService->getBarangKeluarLookup($kodeBarang);

        if (! $data) {
            return response()->json([
                'status' => false,
                'message' => 'Barang tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function show(BarangKeluar $barangKeluar): View
    {
        $barangKeluar->load([
            'details.barang',
            'details.serials',
        ]);

        return view('barang-keluar.show', compact('barangKeluar'));
    }
}
