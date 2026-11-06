<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\BarangMasukSerial;
use App\Models\StokBarang;
use App\Models\StokMutasi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarangImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        $data = $rows
            ->map(fn ($row) => $row->toArray())
            ->filter(fn (array $row) => collect($row)->contains(
                fn ($value) => $value !== null && trim((string) $value) !== ''
            ))
            ->values()
            ->map(function (array $row, int $index): array {
                $clean = [
                    'departemen' => trim((string) ($row['departemen'] ?? '')),
                    'no_barang' => trim((string) ($row['no_barang'] ?? '')),
                    'nama_barang' => trim((string) ($row['deskripsi_barang'] ?? '')),
                    'item_barang' => trim((string) ($row['item_barang'] ?? '')),
                    'nomor_seri' => trim((string) ($row['nomor_seribatch'] ?? '')),
                    'kuantitas' => $this->parseInteger($row['kuantitas'] ?? null),
                    'harga_satuan' => $this->parseMoney($row['harga_satuan'] ?? null),
                    'baris_excel' => $index + 2,
                ];

                $validator = Validator::make($clean, [
                    'departemen' => ['required', 'string', 'max:255'],
                    'no_barang' => ['required', 'string', 'max:50'],
                    'nama_barang' => ['required', 'string', 'max:255'],
                    'item_barang' => ['required', 'string', 'max:100'],
                    'nomor_seri' => ['required', 'string', 'max:255'],
                    'kuantitas' => ['required', 'integer', 'min:1'],
                    'harga_satuan' => ['required', 'numeric', 'min:0'],
                ]);

                if ($validator->fails()) {
                    throw ValidationException::withMessages([
                        'file' => 'Baris Excel ' . $clean['baris_excel'] . ': '
                            . implode(' ', $validator->errors()->all()),
                    ]);
                }

                $identitasBaris = implode('|', [
                    $clean['no_barang'],
                    $clean['item_barang'],
                    $clean['nomor_seri'],
                    $clean['baris_excel'],
                ]);

                $clean['kode_barang'] = substr($clean['no_barang'], 0, 10)
                    . '-'
                    . substr($clean['item_barang'], 0, 10)
                    . '-'
                    . substr(hash('sha256', $identitasBaris), 0, 16);

                return $clean;
            });

        if ($data->isEmpty()) {
            throw ValidationException::withMessages([
                'file' => 'File Excel tidak memiliki baris data.',
            ]);
        }

        DB::transaction(function () use ($data): void {
            /*
             * Hapus hanya snapshot import sebelumnya.
             * Riwayat barang masuk/keluar biasa tidak dihapus.
             */
            $oldTransactions = DB::table('barang_import_logs')
                ->pluck('nomor_transaksi');

            if ($oldTransactions->isNotEmpty()) {
                StokMutasi::query()
                    ->whereIn('nomor_transaksi', $oldTransactions)
                    ->delete();

                $oldHeaders = BarangMasuk::query()
                    ->whereIn('nomor_transaksi', $oldTransactions)
                    ->get();

                foreach ($oldHeaders as $oldHeader) {
                    $oldHeader->delete();
                }

                DB::table('barang_import_logs')
                    ->whereIn('nomor_transaksi', $oldTransactions)
                    ->delete();
            }

            // Jadikan katalog lama tidak aktif dan saldo lamanya nol.
            Barang::query()->update(['is_active' => false]);
            StokBarang::query()->update(['stok' => 0]);

            $nomorTransaksi = BarangMasuk::generateNomor();

            $header = BarangMasuk::create([
                'nomor_transaksi' => $nomorTransaksi,
                'tanggal_masuk' => now()->toDateString(),
                'keterangan' => 'Snapshot stok dari import Excel',
                'operator' => 'Import Excel',
            ]);

            DB::table('barang_import_logs')->insert([
                'nomor_transaksi' => $nomorTransaksi,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $barangs = [];

            foreach ($data as $row) {
                $barangs[$row['baris_excel']] = Barang::updateOrCreate(
                    ['kode_barang' => $row['kode_barang']],
                    [
                        'nama_barang' => $row['nama_barang'],
                        'item_barang' => $row['item_barang'],
                        'barang_induk' => $row['no_barang'],
                        'harga_satuan' => $row['harga_satuan'],
                        'unit' => str_contains(
                            strtoupper($row['item_barang']),
                            '[PC]'
                        ) ? 'pcs' : 'lsn',
                        'departemen' => $row['departemen'],
                        'pakai_no_seri' => true,
                        'is_active' => true,
                    ]
                );
            }

            $batchSequences = [];

            foreach ($data as $row) {
                $barang = $barangs[$row['baris_excel']];
                $qtyLusin = $row['kuantitas'];
                $serialNumber = $row['nomor_seri'];

                if (! array_key_exists($serialNumber, $batchSequences)) {
                    $batchPrefix = 'BM' . $serialNumber;
                    $lastBatch = BarangMasukSerial::query()
                        ->where('serial_number', $serialNumber)
                        ->where('batch', 'like', $batchPrefix . '%')
                        ->orderByDesc('id')
                        ->value('batch');

                    $batchSequences[$serialNumber] = 0;
                    if (is_string($lastBatch)
                        && preg_match('/^' . preg_quote($batchPrefix, '/') . '(\d{4})$/', $lastBatch, $matches)
                    ) {
                        $batchSequences[$serialNumber] = (int) $matches[1];
                    }
                }

                $batchSequences[$serialNumber]++;
                if ($batchSequences[$serialNumber] > 9999) {
                    throw new \RuntimeException(
                        "Urutan batch serial {$serialNumber} sudah melebihi 9999."
                    );
                }
                $batch = 'BM' . $serialNumber . str_pad(
                    (string) $batchSequences[$serialNumber],
                    4,
                    '0',
                    STR_PAD_LEFT
                );

                $detail = BarangMasukDetail::create([
                    'barang_masuk_id' => $header->id,
                    'barang_id' => $barang->id,
                    'qty' => $qtyLusin,
                    'harga_satuan' => $row['harga_satuan'],
                    'batch' => $batch,
                    'serial_number' => $serialNumber,
                ]);

                BarangMasukSerial::create([
                    'barang_masuk_detail_id' => $detail->id,
                    'serial_number' => $serialNumber,
                    'batch' => $batch,
                    'qty' => $qtyLusin,
                ]);

                StokMutasi::create([
                    'barang_id' => $barang->id,
                    'nomor_transaksi' => $nomorTransaksi,
                    'jenis' => 'MASUK',
                    'qty' => $qtyLusin,
                    'batch' => $batch,
                    'serial_number' => $serialNumber,
                    'keterangan' => 'Snapshot stok dari import Excel (lusin)',
                ]);
            }

            // Setiap baris Excel memiliki master dan saldo sendiri.
            foreach ($data as $row) {
                StokBarang::updateOrCreate(
                    ['barang_id' => $barangs[$row['baris_excel']]->id],
                    ['stok' => $row['kuantitas']]
                );
            }
        });
    }

    private function parseInteger(mixed $value): ?int
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $digits = preg_replace('/\D+/', '', (string) $value);

        return $digits === '' ? null : (int) $digits;
    }

    private function parseMoney(mixed $value): int|float|null
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        if (is_numeric($value)) {
            return $value + 0;
        }

        // Contoh "591,000" dan "591.000" dibaca sebagai 591000.
        $digits = preg_replace('/\D+/', '', (string) $value);

        return $digits === '' ? null : (int) $digits;
    }
}
