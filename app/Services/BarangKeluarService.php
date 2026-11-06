<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarDetail;
use App\Models\BarangKeluarSerial;
use App\Models\BarangMasukSerial;
use App\Models\StokBarang;
use App\Models\StokMutasi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BarangKeluarService
{
    public function getPaginatedKeluar(int $perPage = 10): LengthAwarePaginator
    {
        return BarangKeluar::withCount('details')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function generateNomor(): string
    {
        return BarangKeluar::generateNomor();
    }

    public function createKeluar(array $data): void
    {
        DB::transaction(function () use ($data): void {
            $header = BarangKeluar::create([
                'nomor_transaksi' => $data['nomor_transaksi'],
                'tanggal' => $data['tanggal_keluar'],
                'status' => $data['status'] ?? 'pending',
                'courier' => $data['courier'] ?? null,
                'destination' => $data['destination'] ?? null,
                'keterangan' => $data['keterangan'] ?? null,
                'status_updated_at' => now(),
            ]);

            // Mencegah batch yang sama terpakai berlebih dalam satu transaksi.
            $qtyDimintaPerBatch = [];

            foreach ($data['barang_id'] as $index => $barangId) {
                $qty = (int) ($data['qty'][$index] ?? 0);

                if ($qty <= 0) {
                    throw new RuntimeException('Qty barang harus lebih dari 0.');
                }

                $stok = StokBarang::query()
                    ->where('barang_id', $barangId)
                    ->lockForUpdate()
                    ->first();

                if (! $stok || $stok->stok < $qty) {
                    throw new RuntimeException('Stok barang tidak mencukupi.');
                }

                $barang = Barang::query()->find($barangId);

                if (! $barang) {
                    throw new RuntimeException('Barang tidak ditemukan.');
                }

                $serialNumbers = $data['serial_number'][$index] ?? [];
                $serialQtys = $data['serial_qty'][$index] ?? [];
                $serialBatches = $data['serial_batch'][$index] ?? [];
                $serialRows = [];

                foreach ($serialNumbers as $serialIndex => $serialValue) {
                    $serial = trim((string) $serialValue);
                    $batch = trim((string) ($serialBatches[$serialIndex] ?? ''));
                    $serialQty = (int) ($serialQtys[$serialIndex] ?? 0);

                    // Abaikan baris serial yang benar-benar kosong.
                    if ($serial === '' && $batch === '' && $serialQty === 0) {
                        continue;
                    }

                    if ($serial === '' || $batch === '' || $serialQty < 1) {
                        throw new RuntimeException(
                            'Serial, batch, dan qty serial harus terisi.'
                        );
                    }

                    $qtyMasuk = (int) BarangMasukSerial::query()
                        ->where('serial_number', $serial)
                        ->where('batch', $batch)
                        ->whereHas('detail', function ($query) use ($barangId): void {
                            $query->where('barang_id', $barangId);
                        })
                        ->sum('qty');

                    $qtyKeluar = (int) BarangKeluarSerial::query()
                        ->where('serial_number', $serial)
                        ->where('batch', $batch)
                        ->whereHas('barangKeluarDetail', function ($query) use ($barangId): void {
                            $query->where('barang_id', $barangId);
                        })
                        ->sum('qty');

                    $kunciBatch = $barangId . '|' . $serial . '|' . $batch;
                    $qtyDimintaPerBatch[$kunciBatch] =
                        ($qtyDimintaPerBatch[$kunciBatch] ?? 0) + $serialQty;

                    if (
                        $qtyDimintaPerBatch[$kunciBatch]
                        > ($qtyMasuk - $qtyKeluar)
                    ) {
                        throw new RuntimeException(
                            "Stok serial {$serial}, batch {$batch} tidak mencukupi."
                        );
                    }

                    $serialRows[] = [
                        'serial_number' => $serial,
                        'batch' => $batch,
                        'qty' => $serialQty,
                    ];
                }

                if ($barang->pakai_no_seri && $serialRows === []) {
                    throw new RuntimeException(
                        "Serial dan batch wajib dipilih untuk barang {$barang->nama_barang}."
                    );
                }

                if (
                    $serialRows !== []
                    && array_sum(array_column($serialRows, 'qty')) !== $qty
                ) {
                    throw new RuntimeException(
                        'Total Qty Serial harus sama dengan Qty Barang.'
                    );
                }

                $detail = BarangKeluarDetail::create([
                    'barang_keluar_id' => $header->id,
                    'barang_id' => $barangId,
                    'qty' => $qty,
                    'batch' => $data['nomor_transaksi'],
                    'serial_number' => null,
                ]);

                $serialInserts = [];

                foreach ($serialRows as $serialRow) {
                    $serialInserts[] = [
                        'barang_keluar_detail_id' => $detail->id,
                        'serial_number' => $serialRow['serial_number'],
                        'batch' => $serialRow['batch'],
                        'qty' => $serialRow['qty'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if ($serialInserts !== []) {
                    BarangKeluarSerial::insert($serialInserts);
                }

                $stok->decrement('stok', $qty);

                StokMutasi::create([
                    'barang_id' => $barangId,
                    'nomor_transaksi' => $header->nomor_transaksi,
                    'jenis' => 'KELUAR',
                    'qty' => $qty,
                    'batch' => $data['nomor_transaksi'],
                    'serial_number' => null,
                    'keterangan' => 'Barang Keluar Produksi',
                ]);
            }
        });
    }

    public function getBarangKeluarLookup(string $kodeBarang): ?array
    {
        $barcodeMatch = Barang::parseBarcode($kodeBarang);

        $barangQuery = Barang::query()
            ->leftJoin(
                'stok_barangs',
                'stok_barangs.barang_id',
                '=',
                'barangs.id'
            )
            ->select([
                'barangs.id',
                'barangs.kode_barang',
                'barangs.barang_induk',
                'barangs.item_barang',
                'barangs.nama_barang',
                'barangs.unit',
                'barangs.pakai_no_seri',
                'stok_barangs.stok',
            ])
            ->when(
                $barcodeMatch !== null,
                fn ($query) => $query->where('barangs.id', $barcodeMatch['barang']->id),
                fn ($query) => $query->where('barangs.kode_barang', $kodeBarang)
            );

        $barang = $barangQuery->first();

        if (! $barang) {
            return null;
        }

        $masukLots = BarangMasukSerial::query()
            ->join(
                'barang_masuk_details',
                'barang_masuk_details.id',
                '=',
                'barang_masuk_serials.barang_masuk_detail_id'
            )
            ->where('barang_masuk_details.barang_id', $barang->id)
            ->whereNotNull('barang_masuk_serials.batch')
            ->selectRaw(
                'MIN(barang_masuk_serials.id) as first_id,
                 barang_masuk_serials.serial_number,
                 barang_masuk_serials.batch,
                 SUM(barang_masuk_serials.qty) as qty_masuk'
            )
            ->groupBy(
                'barang_masuk_serials.serial_number',
                'barang_masuk_serials.batch'
            )
            ->orderBy('first_id')
            ->get();

        // Jumlah keluar dihitung per serial dan batch, serta dibatasi ke barang ini.
        $keluarLots = BarangKeluarSerial::query()
            ->join(
                'barang_keluar_details',
                'barang_keluar_details.id',
                '=',
                'barang_keluar_serials.barang_keluar_detail_id'
            )
            ->where('barang_keluar_details.barang_id', $barang->id)
            ->whereIn('barang_keluar_serials.batch', $masukLots->pluck('batch'))
            ->selectRaw(
                'barang_keluar_serials.serial_number,
                 barang_keluar_serials.batch,
                 SUM(barang_keluar_serials.qty) as qty_keluar'
            )
            ->groupBy(
                'barang_keluar_serials.serial_number',
                'barang_keluar_serials.batch'
            )
            ->get()
            ->keyBy(
                fn ($lot) => $lot->serial_number . '|' . $lot->batch
            );

        $serials = $masukLots
            ->map(function ($lot) use ($keluarLots): array {
                $key = $lot->serial_number . '|' . $lot->batch;
                $lotKeluar = $keluarLots->get($key);
                $qtyKeluar = (int) ($lotKeluar->qty_keluar ?? 0);

                return [
                    'serial_number' => $lot->serial_number,
                    'batch' => $lot->batch,
                    'qty_tersedia' => max(
                        0,
                        (int) $lot->qty_masuk - $qtyKeluar
                    ),
                ];
            })
            ->filter(fn (array $lot): bool => $lot['qty_tersedia'] > 0)
            ->when(
                $barcodeMatch !== null,
                fn ($lots) => $lots->filter(
                    fn (array $lot): bool =>
                        $lot['serial_number'] === $barcodeMatch['serial_number']
                )->take(1)
                )
            ->values()
            ->all();

        if ($barcodeMatch !== null && $serials === []) {
            return null;
        }

        $pakaiNoSeri = (bool) $barang->pakai_no_seri;
        $stokTersedia = $pakaiNoSeri
        ? array_sum(array_column($serials, 'qty_tersedia'))
        : (int) ($barang->stok ?? 0);

     return [
    'id' => $barang->id,
    'kode_barang' => $barang->kode_tampilan,
    'nama_barang' => $barang->nama_barang,
    'pakai_no_seri' => $pakaiNoSeri,
    'stok' => $stokTersedia,
    'serials' => $serials,
];
    }
}
