<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\StokMutasi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StokMutasiService
{
    public function getFilteredMutasi(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = StokMutasi::with('barang');

        if (!empty($filters['kode_barang'])) {
            $kode = $filters['kode_barang'];
            $query = StokMutasi::join('barangs','stok_mutasis.barang_id','barangs.id')
                ->select('stok_mutasis.*')
                ->where('barangs.kode_barang','like','%'.$kode.'%');
        }

        if (!empty($filters['jenis'])) {
            $query->where('jenis', $filters['jenis']);
        }

        if (!empty($filters['tanggal_awal'])) {
            $query->whereDate('created_at', '>=', $filters['tanggal_awal']);
        }

        if (!empty($filters['tanggal_akhir'])) {
            $query->whereDate('created_at', '<=', $filters['tanggal_akhir']);
        }

        return $query->latest()->paginate($perPage);
    }
}
