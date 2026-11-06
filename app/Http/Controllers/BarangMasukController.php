<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarangMasukStoreRequest;
use App\Models\BarangMasuk;
use App\Services\BarangMasukService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarangMasukController extends Controller
{
    public function __construct(
        protected BarangMasukService $barangMasukService
    ) {}

    public function index(Request $request): View
    {
        $perPage = (int) $request->query('per_page', 10);
        $barangMasuks = $this->barangMasukService->getPaginatedMasuk($perPage);

        return view('barang-masuk.index', compact('barangMasuks'));
    }

    public function scanner(): View
    {
        $nomor = $this->barangMasukService->generateNomor();

        return view('barang-masuk.scanner', compact('nomor'));
    }

    public function store(BarangMasukStoreRequest $request): RedirectResponse
    {
        $header = $this->barangMasukService->createMasuk($request->validated());

        return redirect()->route('barang-masuk.show', $header->id)
            ->with('success', 'Transaksi barang masuk berhasil disimpan.');
    }

    public function show(BarangMasuk $barangMasuk): View
    {
        $barangMasuk->load([
            'details.barang',
            'details.serials',
        ]);

        return view('barang-masuk.show', compact('barangMasuk'));
    }
}