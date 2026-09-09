<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusPengiriman;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarLog;

class BarangKeluarTrackerService
{
    public function updateStatus(
        BarangKeluar $barangKeluar,
        StatusPengiriman $newStatus,
        ?string $keterangan = null,
    ): bool {
        if (! $barangKeluar->canTransitionTo($newStatus)) {
            throw new \RuntimeException(
                "Tidak dapat mengubah status dari {$barangKeluar->status->label()} ke {$newStatus->label()}."
            );
        }

        $barangKeluar->update([
            'status' => $newStatus,
            'keterangan' => $keterangan ?? $barangKeluar->keterangan,
        ]);

        return true;
    }

    public function getTimeline(BarangKeluar $barangKeluar): array
    {
        $logs = $barangKeluar->getTimeline();

        return $logs->map(function (BarangKeluarLog $log): array {
            return [
                'id' => $log->id,
                'status' => $log->status_to,
                'status_enum' => StatusPengiriman::from($log->status_to),
                'tanggal' => $log->tanggal_perubahan,
                'keterangan' => $log->keterangan,
                'user' => $log->user->name ?? 'System',
                'status_from' => $log->status_from,
            ];
        })->toArray();
    }
}
