<?php

namespace App\Observers;

use App\Models\BarangKeluar;
use App\Models\BarangKeluarLog;
use Illuminate\Support\Facades\Auth;

class BarangKeluarObserver
{
    /**
     * Handle the BarangKeluar "updated" event.
     */
    public function updated(BarangKeluar $barangKeluar): void
    {

        if ($barangKeluar->isDirty('status')) {
            $oldStatus = $barangKeluar->getOriginal('status');
            $newStatus = $barangKeluar->status->value;

            BarangKeluarLog::create([
                'barang_keluar_id' => $barangKeluar->id,
                'status_from' => $oldStatus,
                'status_to' => $newStatus,
                'user_id' => Auth::id(),
                'keterangan' => $barangKeluar->isDirty('keterangan')
                    ? $barangKeluar->keterangan
                    : null,
                'tanggal_perubahan' => now(),
            ]);

            BarangKeluar::where('id', $barangKeluar->id)
                ->update(['status_updated_at' => now()]);
        }
    }
}
