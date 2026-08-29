<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokMutasi extends Model
{
    protected $fillable = [
        'barang_id',
        'nomor_transaksi',
        'jenis',
        'qty',
        'batch',
        'serial_number',
        'keterangan',
    ];

    public function barang() {
        return $this->belongsTo(Barang::class);
    }
}
