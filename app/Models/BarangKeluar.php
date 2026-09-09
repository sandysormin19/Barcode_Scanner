<?php

namespace App\Models;

use App\Enums\StatusPengiriman;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarangKeluar extends Model
{
    protected $fillable = [
        'nomor_transaksi',
        'tanggal',
        'status',
        'status_updated_at',
        'courier',
        'destination',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'status' => StatusPengiriman::class,
            'status_updated_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke BarangKeluarDetail
     */
    public function details(): HasMany
    {
        return $this->hasMany(BarangKeluarDetail::class, 'barang_keluar_id');
    }

    /**
     * Relasi ke BarangKeluarLog
     */
    public function logs(): HasMany
    {
        return $this->hasMany(BarangKeluarLog::class, 'barang_keluar_id')
            ->orderBy('tanggal_perubahan', 'asc');
    }

    /**
     * Generate nomor transaksi
     */
    public static function generateNomor(): string
    {
        $prefix = 'BK' . date('Ymd');

        $last = self::whereDate('created_at', today())
            ->latest()
            ->first();

        if (! $last) {
            return $prefix . '0001';
        }

        $nomorTerakhir = substr($last->nomor_transaksi, -4);

        return $prefix . str_pad(
            (int) $nomorTerakhir + 1,
            4,
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * Check apakah dapat mengubah ke status tertentu
     */
    public function canTransitionTo(StatusPengiriman $newStatus): bool
    {
        if ($this->status instanceof StatusPengiriman && $this->status->value === $newStatus->value) {
            return false;
        }

        return in_array($newStatus, $this->status->nextValidStatuses(), true);
    }

    /**
     * Get timeline logs
     */
    public function getTimeline()
    {
        return $this->logs()->with('user')->get();
    }
}
