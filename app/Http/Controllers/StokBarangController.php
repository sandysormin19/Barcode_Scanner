<?php

namespace App\Http\Controllers;

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
        if (! in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 25;
        }

        $search = trim(mb_substr((string) $request->query('search', ''), 0, 100));
        $status = (string) $request->query('status', '');
        if (! in_array($status, ['', 'habis', 'menipis', 'aman'], true)) {
            $status = '';
        }

        $stok = $this->stokBarangService->getPaginatedStok($perPage, $search, $status);
        $ringkasan = $this->stokBarangService->getSummary();
        $batasMenipis = (int) config('stok.low_stock_threshold', 5);

        return view('stok.index', compact('stok', 'ringkasan', 'search', 'status', 'perPage', 'batasMenipis'));
    }
}
