<?php

namespace Tests\Feature\Wms;

use App\Models\Barang;
use App\Models\StokBarang;
use App\Models\StokMutasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangKeluarTanpaSerialTest extends TestCase
{
    use RefreshDatabase;

    public function test_barang_tanpa_serial_dapat_dikeluarkan_dari_scanner(): void
    {
        $barang = Barang::create([
            'kode_barang' => 'ITEM-'.uniqid(),
            'nama_barang' => 'Barang Biasa',
            'unit' => 'pcs',
            'pakai_no_seri' => false,
        ]);
        StokBarang::create(['barang_id' => $barang->id, 'stok' => 5]);

        $response = $this->actingAs(User::factory()->create())->post('/barang-keluar', [
            'nomor_transaksi' => 'BK202609250001',
            'tanggal_keluar' => '2026-09-25',
            'barang_id' => [3 => $barang->id],
            'qty' => [3 => 2],
        ]);

        $response->assertRedirect(route('barang-keluar.index'));
        $this->assertSame(3, (int) StokBarang::where('barang_id', $barang->id)->value('stok'));
        $this->assertSame(1, StokMutasi::where('nomor_transaksi', 'BK202609250001')->count());
    }
}
