<?php

namespace App\Services;

use App\Models\BarangKeluar;
use App\Models\BarangKeluarDetail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EshopeSyncService
{
    protected string $eshopWebhookUrl;

    public function __construct()
    {
        $this->eshopWebhookUrl = config('app.eshope_webhook_url', 'http://eshope.local/api/webhook/delivered');
    }

    /**
     * Kirim notifikasi produk yang telah dikirim ke Eshope
     *
     * @param BarangKeluar $barangKeluar
     * @return bool
     */
    public function syncDeliveredItems(BarangKeluar $barangKeluar): bool
    {
        if ($barangKeluar->status->value !== 'delivered') {
            return false;
        }

        try {
            // Ambil detail barang yang dikirim
            $details = $barangKeluar->details()->with('barang.kategori')->get();

            foreach ($details as $detail) {
                $this->sendProductToEshope($barangKeluar, $detail);
            }

            Log::info('Eshope sync completed', [
                'barang_keluar_id' => $barangKeluar->id,
                'nomor_transaksi' => $barangKeluar->nomor_transaksi,
                'items_count' => $details->count(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Eshope sync failed', [
                'barang_keluar_id' => $barangKeluar->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Kirim detail produk individual ke Eshope
     *
     * @param BarangKeluar $barangKeluar
     * @param BarangKeluarDetail $detail
     * @return void
     */
    private function sendProductToEshope(BarangKeluar $barangKeluar, BarangKeluarDetail $detail): void
    {
        $payload = [
            'barang_id' => $detail->barang_id,
            'nama_barang' => $detail->barang->nama_barang ?? 'Unknown',
            'kategori' => $detail->barang->kategori->nama_kategori ?? 'Uncategorized',
            'harga' => $detail->barang->harga ?? 0,
            'deskripsi' => $detail->barang->deskripsi ?? null,
            'qty' => $detail->qty,
            'wms_nomor_transaksi' => $barangKeluar->nomor_transaksi,
        ];

        try {
            $response = Http::timeout(10)->post($this->eshopWebhookUrl, $payload);

            if ($response->successful()) {
                Log::info('Product synced to Eshope', [
                    'barang_id' => $detail->barang_id,
                    'nomor_transaksi' => $barangKeluar->nomor_transaksi,
                ]);
            } else {
                Log::warning('Eshope webhook returned error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send product to Eshope', [
                'barang_id' => $detail->barang_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Test koneksi ke Eshope
     *
     * @return bool
     */
    public function testConnection(): bool
    {
        try {
            $healthCheckUrl = str_replace('/delivered', '/health', $this->eshopWebhookUrl);
            $response = Http::timeout(5)->get($healthCheckUrl);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Eshope health check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
