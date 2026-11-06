<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Barang;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StokBarangService
{
    public function getSummary(): object
    {
        $batasMenipis = (int) config('stok.low_stock_threshold', 5);

        return Barang::query()
            ->leftJoin('stok_barangs', 'stok_barangs.barang_id', '=', 'barangs.id')
            ->where('barangs.is_active', true)
            ->selectRaw('
                COUNT(*) as total_barang,
                SUM(CASE WHEN COALESCE(stok_barangs.stok, 0) <= 0 THEN 1 ELSE 0 END) as stok_habis,
                SUM(CASE WHEN COALESCE(stok_barangs.stok, 0) > 0
                    AND stok_barangs.stok <= ? THEN 1 ELSE 0 END) as stok_menipis,
                SUM(CASE WHEN COALESCE(stok_barangs.stok, 0) > ? THEN 1 ELSE 0 END) as stok_aman
            ', [$batasMenipis, $batasMenipis])
            ->first();
    }

    public function getPaginatedStok(
        int $perPage = 25,
        string $search = '',
        string $status = ''
    ): LengthAwarePaginator {
        $batasMenipis = (int) config('stok.low_stock_threshold', 5);

        $query = Barang::query()
            ->leftJoin('stok_barangs', 'stok_barangs.barang_id', '=', 'barangs.id')
            ->with('stok')
            ->where('barangs.is_active', true)
            ->select('barangs.*');

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('barangs.kode_barang', 'like', "%{$search}%")
                    ->orWhere('barangs.barang_induk', 'like', "%{$search}%")
                    ->orWhere('barangs.item_barang', 'like', "%{$search}%")
                    ->orWhere('barangs.nama_barang', 'like', "%{$search}%");
            });
        }

        match ($status) {
            'habis' => $query->where(function ($builder): void {
                $builder->whereNull('stok_barangs.id')
                    ->orWhere('stok_barangs.stok', '<=', 0);
            }),
            'menipis' => $query->where('stok_barangs.stok', '>', 0)
                ->where('stok_barangs.stok', '<=', $batasMenipis),
            'aman' => $query->where('stok_barangs.stok', '>', $batasMenipis),
            default => null,
        };

        return $query
            ->orderByRaw('CASE
                WHEN COALESCE(stok_barangs.stok, 0) <= 0 THEN 0
                WHEN stok_barangs.stok <= ? THEN 1
                ELSE 2
            END', [$batasMenipis])
            ->orderBy('barangs.nama_barang')
            ->paginate($perPage)
            ->withQueryString();
    }
}
