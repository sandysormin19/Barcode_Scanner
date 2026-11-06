<?php

namespace Tests\Feature\Wms;

use App\Models\Barang;
use App\Models\StokBarang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangMasukSerialValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_barang_tanpa_serial_dapat_diterima(): void
    {
        $barang = $this->barang();

        $response = $this->actingAs(User::factory()->create())->post('/barang-masuk', [
            'nomor_transaksi' => 'BM202609250001',
            'tanggal_masuk' => '2026-09-25',
            'barang_id' => [0 => $barang->id],
            'qty' => [0 => 5],
        ]);

        $response->assertRedirect();
        $this->assertSame(5, (int) StokBarang::where('barang_id', $barang->id)->value('stok'));
    }

    public function test_serial_qty_harus_sama_dengan_qty_barang(): void
    {
        $barang = $this->barang(true);

        $response = $this->actingAs(User::factory()->create())->post('/barang-masuk', [
            'nomor_transaksi' => 'BM202609250002',
            'tanggal_masuk' => '2026-09-25',
            'barang_id' => [4 => $barang->id],
            'qty' => [4 => 2],
            'serial_number' => [4 => ['SN-1']],
            'serial_qty' => [4 => [1]],
        ]);

        $response->assertSessionHasErrors('serial_qty.4');
        $this->assertDatabaseMissing('barang_masuks', ['nomor_transaksi' => 'BM202609250002']);
    }

    public function test_serial_ganda_dalam_transaksi_ditolak(): void
    {
        $barang = $this->barang(true);

        $response = $this->actingAs(User::factory()->create())->post('/barang-masuk', [
            'nomor_transaksi' => 'BM202609250003',
            'tanggal_masuk' => '2026-09-25',
            'barang_id' => [0 => $barang->id],
            'qty' => [0 => 2],
            'serial_number' => [0 => ['SN-DUP', 'SN-DUP']],
            'serial_qty' => [0 => [1, 1]],
        ]);

        $response->assertSessionHasErrors('serial_number.0.1');
        $this->assertDatabaseMissing('barang_masuks', ['nomor_transaksi' => 'BM202609250003']);
    }

    private function barang(bool $pakaiNoSeri = false): Barang
    {
        return Barang::create([
            'kode_barang' => 'ITEM-'.uniqid(),
            'nama_barang' => 'Barang Test',
            'unit' => 'pcs',
            'pakai_no_seri' => $pakaiNoSeri,
        ]);
    }
}
