<?php

namespace App\Http\Controllers;

use App\Models\StokBarang;
use App\Services\StokBarangService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StokBarangController extends Controller
{
    public function __construct(
        protected StokBarangService $stokBarangService
    ) {}

    public function index(Request $request): View
    {
        $perPage = (int) $request->query('per_page', 25);

        $stok = $this->stokBarangService->getPaginatedStok($perPage);

        return view('stok.index', compact('stok'));
    }
}
