<?php

namespace App\Http\Controllers;

use App\Http\Requests\KategoriStoreRequest;
use App\Services\KategoriService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RealRashid\SweetAlert\Facades\Alert;

class KategoriController extends Controller
{
    public function __construct(
        protected KategoriService $kategoriService
    ) {}

    public function index(Request $request): View
    {
        $perPage = (int) $request->query('per_page', 25);

        $kategoris = $this->kategoriService->getPaginatedKategori($perPage);

        return view('kategori.index', compact('kategoris'));
    }

    public function store(KategoriStoreRequest $request): RedirectResponse
    {
        $this->kategoriService->createKategori($request->validated());

        Alert::success('Berhasil', 'Kategori berhasil ditambahkan.');

        return redirect()->route('kategori.index');
    }

}
