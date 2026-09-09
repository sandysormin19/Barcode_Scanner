<?php

namespace App\Http\Controllers;

use App\Enums\StatusPengiriman;
use App\Http\Requests\BarangKeluarStoreRequest;
use App\Models\BarangKeluar;
use App\Services\BarangKeluarService;
use App\Services\BarangKeluarTrackerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarangKeluarController extends Controller
{
    public function __construct(
        protected BarangKeluarService $barangKeluarService,
        protected BarangKeluarTrackerService $trackerService
    ) {}

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
            'logs.user',
        ]);

        $timeline = $this->trackerService->getTimeline($barangKeluar);

        return view('barang-keluar.show', compact('barangKeluar', 'timeline'));
    }

    /**
     * Halaman tracking untuk detail barang keluar
     */
    public function track(BarangKeluar $barangKeluar): View
    {
        $barangKeluar->load('logs.user');
        $timeline = $this->trackerService->getTimeline($barangKeluar);

        return view('barang-keluar.track', compact('barangKeluar', 'timeline'));
    }

    /**
     * Halaman cek status barang keluar berdasarkan nomor transaksi
     */
    public function cekStatus(Request $request): View
    {
        $nomorTransaksi = trim((string) $request->query('nomor_transaksi', ''));
        $barangKeluar = null;
        $timeline = [];

        if ($nomorTransaksi !== '') {
            $barangKeluar = BarangKeluar::query()
                ->where('nomor_transaksi', $nomorTransaksi)
                ->with('logs.user')
                ->first();

            if ($barangKeluar) {
                $timeline = $this->trackerService->getTimeline($barangKeluar);
            }
        }

        return view('barang-keluar.cek-status', compact('barangKeluar', 'timeline', 'nomorTransaksi'));
    }

    /**
     * API untuk update status (JSON)
     */
    public function updateStatus(Request $request, BarangKeluar $barangKeluar): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $newStatus = StatusPengiriman::from($validated['status']);
            $this->trackerService->updateStatus($barangKeluar, $newStatus, $validated['keterangan'] ?? null);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui.',
                'data' => [
                    'status' => $barangKeluar->fresh()->status->value,
                    'status_label' => $barangKeluar->fresh()->status->label(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
