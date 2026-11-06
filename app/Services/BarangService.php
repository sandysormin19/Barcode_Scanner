<?php

declare(strict_types=1);

namespace App\Services;

use App\Imports\BarangImport;
use App\Models\Barang;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Models\BarangMasukSerial;

class BarangService
{
    private BarcodeGeneratorPNG $generator;

    public function __construct()
    {
        $this->generator = new BarcodeGeneratorPNG();
    }

   public function getPaginatedBarang(int $perPage = 25, string $search = ''): LengthAwarePaginator
{
    return Barang::query()
        ->with('barangMasukDetails.serials')
        ->where('is_active', true)
        ->when($search !== '', function ($query) use ($search): void {
            $query->where(function ($query) use ($search): void {
                $query->where('barang_induk', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%")
                    ->orWhere('item_barang', 'like', "%{$search}%")
                    ->orWhere('nama_barang', 'like', "%{$search}%")
                    ->orWhereHas('barangMasukDetails.serials', function($serialQuery)use ($search): void{
                        $serialQuery->where('serial_number', 'like', "%{$search}%")
                        ->orWhere('batch', 'like', "%{$search}%");
                    });
            });
        })
        ->select(['id', 'kode_barang', 'barang_induk', 'item_barang', 'nama_barang', 'unit', 'is_active'])
        ->orderBy('nama_barang')
        ->paginate($perPage)
        ->withQueryString();
}

    public function importFromExcel(UploadedFile|string $file): void
    {
        Excel::import(new BarangImport(), $file);
    }

    public function findByKodeBarang(string $kode): ?Barang
    {
        return Barang::query()
            ->select(['id', 'kode_barang', 'item_barang', 'nama_barang', 'unit', 'pakai_no_seri'])
            ->where('kode_barang', $kode)
            ->first();
    }

    /**
     * Membuat label untuk setiap serial, lalu mengulang label sesuai jumlah cetak.
     *
     * @param array<int|string>|null $ids
     * @param array<int|string, array<int, string|null>> $serialNumbers
     * @param array<int|string, array<int, int|string|null>> $jumlahCetak
     * @return array<int, array<string, string>>
     */


    public function generateBarcodeSet(?array $ids, array $jumlahCetak): array
    {
        if (empty($ids)) {
            return [];
        }

        $barangs = Barang::query()
            ->with(['barangMasukDetails.serials' => function ($query): void{
                $query->whereNotNull('batch')->orderByDesc('id');
            }])
            ->select([
                'id',
                'item_barang',
                'nama_barang',
            ])
            ->where('is_active', true)
            ->whereIn('id', $ids)
            ->get();

        $labels = [];

        foreach ($barangs as $barang) {
            $lot = $barang->barangMasukDetails
            ->flatMap(fn ($detail) => $detail->serials)
            ->sortByDesc('id')
            ->first();

            if ($lot === null) {
              continue;
                }

            $itemBarang = trim((string) $barang->item_barang);
            $serialNumber = trim((string) $lot->serial_number);
            $batch = trim((string) $lot->batch);

            // Tampilkan format baru juga untuk data lama yang batch-nya duplikat serial.
            $batchPattern = '/^BM' . preg_quote($serialNumber, '/') . '\d{4}$/';
            if (! preg_match($batchPattern, $batch)) {
                $sequence = BarangMasukSerial::query()
                    ->where('serial_number', $serialNumber)
                    ->where('id', '<=', $lot->id)
                    ->count();
                $batch = 'BM' . $serialNumber . str_pad(
                    (string) $sequence,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }

            if ($itemBarang === '' || $serialNumber === '' || $batch === '') {
            continue;
        }

            $size = $this->extractSize((string) $barang->nama_barang);


            // Format barcode sesuai Excel: Item Barang-Nomor Seri.
            $barcodeValue = $itemBarang . '|' . $serialNumber;
        $barcodeImage = base64_encode(
            $this->generator->getBarcode(
                $barcodeValue,
                $this->generator::TYPE_CODE_128,
                2,
                70
            )
        );
            $batchPrefix = 'BM' . $serialNumber;
            $batchAwal = 1;
            if (preg_match(
                '/^' . preg_quote($batchPrefix, '/') . '(\d{4})$/',
                $batch,
                $matches
            )) {
                $batchAwal = (int) $matches[1];
            }

            $copies = max(1, (int) ($jumlahCetak[$barang->id] ?? 1));

            for ($copy = 1; $copy <= $copies; $copy++) {
                $nomorUrutBatch = $batchAwal + $copy - 1;
                $batchLabel = $batchPrefix . str_pad(
                    (string) $nomorUrutBatch,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

                $labels[] = [
                    'batch' => $batchLabel,
                    'item_barang' => $barang->item_barang,
                    'serial_number' => $serialNumber,
                    'size' => $size,
                    'barcode_value' => $barcodeValue,
                    'barcode' => $barcodeImage,
                ];
            }
        }
        return $labels;
    }

    private function extractSize(string $deskripsi): string
    {
        $deskripsi = strtoupper($deskripsi);
        $sizeToken = '(?:XXXS|XXS|XXXL|XXL|3XL|2XL|XS|XL|L|M|S)';

        // Ambil semua ukuran, termasuk rentang seperti M-L yang diikuti XL.
        preg_match_all(
            '/(?<![A-Z0-9])(' . $sizeToken . '(?:\s*-\s*' . $sizeToken . ')*)(?![A-Z0-9])/',
            $deskripsi,
            $matches
        );

        $sizes = array_map(
            static fn (string $size): string => preg_replace('/\s*-\s*/', '-', $size) ?? $size,
            $matches[1] ?? []
        );

        return implode(' ', array_unique($sizes));
    }

}
