<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\BarangKeluar;
use App\Models\BarangKeluarDetail;
use App\Models\BarangKeluarSerial;
use App\Models\BarangMasukSerial;
use App\Models\StokBarang;
use App\Models\StokMutasi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class BarangKeluarService
{
    public function getPaginatedKeluar(int $perPage = 10): LengthAwarePaginator
    {
        return BarangKeluar::withCount('details')->orderByDesc('id')->paginate($perPage);
    }

    public function generateNomor(): string
    {
        return BarangKeluar::generateNomor();
    }

    /**
     * Create barang keluar transaction. $data must be validated.
     */
    public function createKeluar(array $data): void
    {
        // pre-aggregate serial sums for all serials referenced by request
        $allSerials = [];
        foreach ($data['serial_number'] as $arr) {
            foreach ($arr as $s) {
                $v = trim((string) $s);
                if ($v !== '') $allSerials[$v] = $v;
            }
        }
        $serialList = array_values($allSerials);

        $masukSums = [];
        $keluarSums = [];

        if (!empty($serialList)) {
            $masukSums = BarangMasukSerial::query()
                ->join('barang_masuk_details','barang_masuk_serials.barang_masuk_detail_id','=','barang_masuk_details.id')
                ->whereIn('barang_masuk_serials.serial_number', $serialList)
                ->groupBy('barang_masuk_serials.serial_number')
                ->selectRaw('barang_masuk_serials.serial_number, SUM(barang_masuk_serials.qty) as qty')
                ->pluck('qty','serial_number')
                ->toArray();

            $keluarSums = BarangKeluarSerial::whereIn('serial_number', $serialList)
                ->groupBy('serial_number')
                ->selectRaw('serial_number, SUM(qty) as qty')
                ->pluck('qty','serial_number')
                ->toArray();
        }

        DB::transaction(function () use ($data, $masukSums, $keluarSums) {
            $header = BarangKeluar::create([
                'nomor_transaksi' => $data['nomor_transaksi'],
                'tanggal' => $data['tanggal_keluar'],
                'status' => $data['status'] ?? 'pending',
                'courier' => $data['courier'] ?? null,
                'destination' => $data['destination'] ?? null,
                'keterangan' => $data['keterangan'] ?? null,
                'status_updated_at' => now(),
            ]);

            foreach ($data['barang_id'] as $index => $barangId) {
                $qty = (int) ($data['qty'][$index] ?? 0);
                if ($qty <= 0) throw new \Exception('Qty barang harus lebih dari 0.');

                $stok = StokBarang::where('barang_id', $barangId)->lockForUpdate()->first();
                if (!$stok || $stok->stok < $qty) throw new \Exception('Stok barang tidak mencukupi.');

                $serialNumbers = $data['serial_number'][$index] ?? [];
                $serialQtys = $data['serial_qty'][$index] ?? [];

                // validate serials using pre-aggregates
                $serialClean = array_values(array_filter(array_map('trim', $serialNumbers), fn($v) => $v !== ''));
                if (!empty($serialClean)) {
                    $totalSerial = array_sum(array_map('intval', $serialQtys));
                    if ($totalSerial !== $qty) throw new \Exception('Total Qty Serial harus sama dengan Qty Barang.');

                    foreach ($serialClean as $k => $sn) {
                        $rq = (int) ($serialQtys[$k] ?? 0);
                        $masuk = (int) ($masukSums[$sn] ?? 0);
                        $keluar = (int) ($keluarSums[$sn] ?? 0);
                        if ($rq > ($masuk - $keluar)) {
                            throw new \Exception("Serial {$sn} melebihi stok tersedia.");
                        }
                    }
                }

                $detail = BarangKeluarDetail::create([
                    'barang_keluar_id' => $header->id,
                    'barang_id' => $barangId,
                    'qty' => $qty,
                    'batch' => $data['nomor_transaksi'],
                    'serial_number' => null,
                ]);

                $bulk = [];
                foreach ($serialNumbers as $sIdx => $sVal) {
                    $sVal = trim((string) $sVal);
                    if ($sVal === '') continue;
                    $bulk[] = [
                        'barang_keluar_detail_id' => $detail->id,
                        'serial_number' => $sVal,
                        'qty' => (int) ($serialQtys[$sIdx] ?? 0),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if (!empty($bulk)) BarangKeluarSerial::insert($bulk);

                $stok->decrement('stok', $qty);

                StokMutasi::create([
                    'barang_id' => $barangId,
                    'nomor_transaksi' => $header->nomor_transaksi,
                    'jenis' => 'KELUAR',
                    'qty' => $qty,
                    'batch' => $data['nomor_transaksi'] ?? null,
                    'serial_number' => null,
                    'keterangan' => 'Barang Keluar Produksi',
                ]);
            }
        });
    }

    public function getBarangKeluarLookup(string $kodeBarang): ?array
    {
        $barang = \App\Models\Barang::select('id','kode_barang','nama_barang','unit')->where('kode_barang', $kodeBarang)->first();
        if (!$barang) return null;

        $stok = StokBarang::where('barang_id', $barang->id)->first();

        // aggregate serials masuk
        $serials = BarangMasukSerial::query()
            ->join('barang_masuk_details','barang_masuk_details.id','=','barang_masuk_serials.barang_masuk_detail_id')
            ->where('barang_masuk_details.barang_id', $barang->id)
            ->select('barang_masuk_serials.serial_number', DB::raw('SUM(barang_masuk_serials.qty) as qty_masuk'))
            ->groupBy('barang_masuk_serials.serial_number')
            ->get();

        $serialKeluar = BarangKeluarSerial::whereIn('serial_number', $serials->pluck('serial_number'))
            ->select('serial_number', DB::raw('SUM(qty) as qty_keluar'))
            ->groupBy('serial_number')
            ->pluck('qty_keluar','serial_number');

        $serials = $serials->map(function ($s) use ($serialKeluar) {
            $masuk = (int) $s->qty_masuk;
            $keluar = (int) ($serialKeluar[$s->serial_number] ?? 0);
            $tersedia = max(0, $masuk - $keluar);
            return ['serial_number'=>$s->serial_number,'qty_masuk'=>$masuk,'qty_keluar'=>$keluar,'qty_tersedia'=>$tersedia];
        })->filter(fn($r) => $r['qty_tersedia'] > 0)->values()->all();

        return [
            'id' => $barang->id,
            'kode_barang' => $barang->kode_barang,
            'nama_barang' => $barang->nama_barang,
            'stok' => $stok ? $stok->stok : 0,
            'serials' => $serials,
        ];
    }
}
