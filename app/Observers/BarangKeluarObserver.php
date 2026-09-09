<?php

namespace App\Observers;

use App\Enums\StatusPengiriman;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarLog;
use Illuminate\Support\Facades\Auth;

class BarangKeluarObserver
{
    public function creating(BarangKeluar $barangKeluar): void
    {
        if (empty($barangKeluar->status)) {
            $barangKeluar->status = StatusPengiriman::Pending;
        }

        if (empty($barangKeluar->status_updated_at)) {
            $barangKeluar->status_updated_at = now();
        }
    }

    /**
     * Handle the BarangKeluar "created" event.
     */
    public function created(BarangKeluar $barangKeluar): void
    {
        $this->writeStatusLog($barangKeluar, null, $barangKeluar->status, $barangKeluar->keterangan ?? null);
    }

    /**
     * Handle the BarangKeluar "updated" event.
     */
    public function updated(BarangKeluar $barangKeluar): void
    {
        if (! $barangKeluar->isDirty('status')) {
            return;
        }

        $this->writeStatusLog(
            $barangKeluar,
            $barangKeluar->getOriginal('status'),
            $barangKeluar->status,
            $barangKeluar->keterangan ?? null,
        );
    }

    protected function writeStatusLog(
        BarangKeluar $barangKeluar,
        StatusPengiriman|string|null $fromStatus,
        StatusPengiriman|string|null $toStatus,
        ?string $keterangan = null,
    ): void {
        $statusFrom = $fromStatus instanceof StatusPengiriman ? $fromStatus->value : $fromStatus;
        $statusTo = $toStatus instanceof StatusPengiriman ? $toStatus->value : $toStatus;

        BarangKeluarLog::create([
            'barang_keluar_id' => $barangKeluar->id,
            'status_from' => $statusFrom,
            'status_to' => $statusTo,
            'keterangan' => $keterangan,
            'user_id' => Auth::id(),
            'tanggal_perubahan' => now(),
        ]);

        $barangKeluar->forceFill([
            'status_updated_at' => now(),
        ])->saveQuietly();
    }

    /**
     * Handle the BarangKeluar "deleted" event.
     */
    public function deleted(BarangKeluar $barangKeluar): void
    {
        //
    }

    /**
     * Handle the BarangKeluar "restored" event.
     */
    public function restored(BarangKeluar $barangKeluar): void
    {
        //
    }

    /**
     * Handle the BarangKeluar "force deleted" event.
     */
    public function forceDeleted(BarangKeluar $barangKeluar): void
    {
        //
    }
}
