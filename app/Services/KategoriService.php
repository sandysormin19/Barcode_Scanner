<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Kategori;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class KategoriService
{
    public function getPaginatedKategori(int $perPage = 25): LengthAwarePaginator
    {
        return Kategori::select('id','nama_kategori','deskripsi','is_active')
            ->orderBy('nama_kategori')
            ->paginate($perPage);
    }

    public function createKategori(array $data): Kategori
    {
        return Kategori::create([
            'nama_kategori' => $data['nama_kategori'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);
    }
}
