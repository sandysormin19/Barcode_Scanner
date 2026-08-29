<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Barang;
use App\Models\StokBarang;
use App\Models\StokMutasi;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function getSummary(): array
    {
        return Cache::remember('dashboard.summary', 30, function () {
            return [
                'totalBarang' => Barang::count(),
                'totalStok' => StokBarang::sum('stok'),
                'barangMasukHariIni' => StokMutasi::where('jenis','MASUK')->whereDate('created_at', today())->sum('qty'),
                'barangKeluarHariIni' => StokMutasi::where('jenis','KELUAR')->whereDate('created_at', today())->sum('qty'),
            ];
        });
    }
}
