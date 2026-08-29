<?php

namespace App\Http\Controllers;

use App\Http\Requests\KategoriStoreRequest;
use App\Models\Kategori;
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

    public function show(Kategori $kategori): View
    {
        return view('kategori.show', compact('kategori'));
    }

    public function edit(Kategori $kategori): View
    {
        return view('kategori.edit', compact('kategori'));
    }

    public function update(KategoriStoreRequest $request, Kategori $kategori): RedirectResponse
    {
        $this->kategoriService->updateKategori($kategori, $request->validated());

        Alert::success('Berhasil', 'Kategori berhasil diperbarui.');

        return redirect()->route('kategori.index');
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        $this->kategoriService->deleteKategori($kategori);

        Alert::success('Berhasil', 'Kategori berhasil dihapus.');

        return redirect()->route('kategori.index');
    }
}
