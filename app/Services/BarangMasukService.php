<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\BarangMasukSerial;
use App\Models\StokBarang;
use App\Models\StokMutasi;
use App\Models\Barang;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BarangMasukService
{
    public function getPaginatedMasuk(int $perPage = 10): LengthAwarePaginator
    {
        return BarangMasuk::withCount('details')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function generateNomor(): string
    {
        return BarangMasuk::generateNomor();
    }

    public function createMasuk(array $data): BarangMasuk
    {
        return DB::transaction(function () use ($data) {

            $header = BarangMasuk::create([
                'nomor_transaksi' => $data['nomor_transaksi'],
                'tanggal_masuk' => $data['tanggal_masuk'],
                'keterangan' => $data['keterangan'] ?? null,
                'operator' => $data['operator'] ?? null,
            ]);


            $barangs = Barang::query()
                ->whereIn('id', $data['barang_id'])
                ->get(['id', 'pakai_no_seri'])
                ->keyBy('id');

            foreach ($data['barang_id'] as $index => $barangId) {

                $qtyBarang = (int) ($data['qty'][$index] ?? 0);
                $serialNumbers = $data['serial_number'][$index] ?? [];
                $serialQtys = $data['serial_qty'][$index] ?? [];
                $serialBatches = $data['serial_batch'][$index] ?? [];

                $detail = BarangMasukDetail::create([
                    'barang_masuk_id' => $header->id,
                    'barang_id' => $barangId,
                    'qty' => $qtyBarang,
                    'batch' => $data['nomor_transaksi'],
                ]);

                $barang = $barangs->get($barangId);

                $serialInserts = [];

                foreach ($serialNumbers as $sIdx => $sVal) {

                    $serial = trim((string) $sVal);
                    $qtySerial = (int) ($serialQtys[$sIdx] ?? 0);

                    if ($serial === '' || $qtySerial < 1) {
                        continue;
                    }

                    $scannedBatch = trim((string) ($serialBatches[$sIdx] ?? ''));

                    if ($scannedBatch !== '') {
                        $serialInserts[] = [
                            'barang_masuk_detail_id' => $detail->id,
                            'serial_number' => $serial,
                            'batch' => $scannedBatch,
                            'qty' => $qtySerial,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        continue;
                    }

                    // Serial manual tanpa batch dari barcode: buat batch berurutan.
                    $batchPrefix = 'BM' . $serial;
                    $lastBatch = BarangMasukSerial::query()
                        ->where('serial_number', $serial)
                        ->where('batch', 'like', $batchPrefix . '%')
                        ->orderByDesc('id')
                        ->value('batch');

                    $lastSequence = 0;

                    if (is_string($lastBatch)
                        && preg_match('/^' . preg_quote($batchPrefix, '/') . '(\d{4})$/', $lastBatch, $matches)
                    ) {
                        $lastSequence = (int) $matches[1];
                    }

                    for ($unit = 1; $unit <= $qtySerial; $unit++) {
                        $sequence = $lastSequence + $unit;
                        if ($sequence > 9999) {
                            throw new \RuntimeException(
                                "Urutan batch serial {$serial} sudah melebihi 9999."
                            );
                        }

                        $serialInserts[] = [
                            'barang_masuk_detail_id' => $detail->id,
                            'serial_number' => $serial,
                            'batch' => $batchPrefix . str_pad(
                                (string) $sequence,
                                4,
                                '0',
                                STR_PAD_LEFT
                            ),
                            'qty' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if ($serialInserts !== []) {
                    BarangMasukSerial::insert($serialInserts);
                }

                $stok = StokBarang::firstOrCreate(
                    ['barang_id' => $barangId],
                    ['stok' => 0]
                );

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
