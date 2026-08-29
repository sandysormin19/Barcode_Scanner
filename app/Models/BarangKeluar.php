<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BarangKeluarDetail;

class BarangKeluar extends Model
{
    protected $fillable = [
        'nomor_transaksi',
        'tanggal',
        'keterangan',
    ];


    public function details()
    {
        return $this->hasMany(BarangKeluarDetail::class,
        'barang_keluar_id');
    }

    public static function generateNomor()
    {
        $prefix = 'BK' . date('Ymd');

        $last = self::whereDate('created_at', today())
                    ->latest()
                    ->first();

        if (!$last) {
            return $prefix . '0001';
        }

        $nomorTerakhir = substr($last->nomor_transaksi, -4);

        return $prefix . str_pad(
            $nomorTerakhir + 1,
            4,
           '0',
            STR_PAD_LEFT
        );
    }
}
