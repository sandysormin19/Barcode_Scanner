<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluarSerial extends Model
{
    protected $fillable = [
        'barang_keluar_detail_id',
        'serial_number',
        'batch',
        'qty',
    ];

    public function barangKeluarDetail()
    {
        return $this->belongsTo(
            BarangKeluarDetail::class,
            'barang_keluar_detail_id'
        );
    }
}
