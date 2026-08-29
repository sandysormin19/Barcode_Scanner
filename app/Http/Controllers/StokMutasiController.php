<?php

namespace App\Http\Controllers;

use App\Services\StokMutasiService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StokMutasiController extends Controller
{
    public function __construct(
        protected StokMutasiService $stokMutasiService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only([
            'kode_barang',
            'jenis',
            'tanggal_awal',
            'tanggal_akhir',
        ]);

        $perPage = (int) $request->query('per_page', 20);

        $mutasis = $this->stokMutasiService->getFilteredMutasi($filters, $perPage);

        return view('stok-mutasi.index', compact('mutasis'));
    }
}
