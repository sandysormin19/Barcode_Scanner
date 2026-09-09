<?php
declare(strict_types=1);

namespace App\Services;

use App\Imports\BarangImport;
use App\Models\Barang;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BarangService
{
    public function getPaginatedBarang(int $perPage = 25): LengthAwarePaginator
    {
        return Barang::select('id','kode_barang','nama_barang','unit','is_active')
            ->orderBy('nama_barang')
            ->paginate($perPage);
    }

    public function importFromExcel($file): void
    {
        // delegate to Maatwebsite's queued import
        Excel::import(new BarangImport, $file);
    }

    public function findByKodeBarang(string $kode): ?Barang
    {
        return Barang::select('id','kode_barang','nama_barang','unit')
            ->where('kode_barang', $kode)
            ->first();
    }

    public function generateBarcode(string $kode): string
    {
        $generator = new BarcodeGeneratorPNG();
        return base64_encode($generator->getBarcode($kode, $generator::TYPE_CODE_128, 2, 50));
    }

    /**
     * Generate barcodes for a set of Barang IDs and return array
     * @param array<int>|null $ids
     * @return array
     */
    public function generateBarcodeSet(?array $ids): array
    {
        $generator = new BarcodeGeneratorPNG();
        $barangs = Barang::select('id','kode_barang','nama_barang')
            ->whereIn('id', $ids ?? [])
            ->get()
            ->map(function (Barang $b) use ($generator) {
                $b->barcode = base64_encode($generator->getBarcode($b->kode_barang, $generator::TYPE_CODE_128, 3, 70));
                return $b;
            })->all();

        return $barangs;
    }
}
