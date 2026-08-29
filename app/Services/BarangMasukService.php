<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\BarangMasukSerial;
use App\Models\StokBarang;
use App\Models\StokMutasi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BarangMasukService
{
    public function getPaginatedMasuk(int $perPage = 10): LengthAwarePaginator
    {
        return BarangMasuk::withCount('details')->orderByDesc('id')->paginate($perPage);
    }

    public function generateNomor(): string
    {
        // delegate to model helper if exists
        return BarangMasuk::generateNomor();
    }

    /**
     * Create barang masuk transaction with details, serials, stok and mutasi.
     * $data must be validated shape from FormRequest
     */
    public function createMasuk(array $data): BarangMasuk
    {
        return DB::transaction(function () use ($data) {
            $header = BarangMasuk::create([
                'nomor_transaksi' => $data['nomor_transaksi'],
                'tanggal_masuk' => $data['tanggal_masuk'],
                'keterangan' => $data['keterangan'] ?? null,
                'operator' => $data['operator'] ?? null,
            ]);

            foreach ($data['barang_id'] as $index => $barangId) {
                $qtyBarang = (int) ($data['qty'][$index] ?? 0);
                $serialNumbers = $data['serial_number'][$index] ?? [];
                $serialQtys = $data['serial_qty'][$index] ?? [];

                $detail = BarangMasukDetail::create([
                    'barang_masuk_id' => $header->id,
                    'barang_id' => $barangId,
                    'qty' => $qtyBarang,
                    'batch' => $data['nomor_transaksi'],
                ]);

                // bulk insert serials if any
                $serialInserts = [];
                foreach ($serialNumbers as $sIdx => $sVal) {
                    $serialInserts[] = [
                        'barang_masuk_detail_id' => $detail->id,
                        'serial_number' => trim($sVal),
                        'qty' => (int) ($serialQtys[$sIdx] ?? 0),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if (!empty($serialInserts)) {
                    BarangMasukSerial::insert($serialInserts);
                }

                $stok = StokBarang::firstOrCreate(['barang_id' => $barangId], ['stok' => 0]);
                $stok->increment('stok', $qtyBarang);

                StokMutasi::create([
                    'barang_id' => $barangId,
                    'nomor_transaksi' => $header->nomor_transaksi,
                    'jenis' => 'MASUK',
                    'qty' => $qtyBarang,
                    'batch' => $data['nomor_transaksi'] ?? null,
                    'serial_number' => null,
                    'keterangan' => 'Barang Masuk Produksi',
                ]);
            }

            return $header;
        });
    }
}
