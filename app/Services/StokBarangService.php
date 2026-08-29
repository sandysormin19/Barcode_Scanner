<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\StokBarang;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StokBarangService
{
    public function getPaginatedStok(int $perPage = 25): LengthAwarePaginator
    {
        return StokBarang::with('barang:id,kode_barang,nama_barang')
            ->orderByDesc('updated_at')
            ->paginate($perPage);
    }
}
