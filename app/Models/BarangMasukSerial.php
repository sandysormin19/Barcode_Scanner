<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasukSerial extends Model
{
    protected $fillable = [
        'barang_masuk_detail_id',
        'serial_number',
        'batch',
        'qty',
    ];

    public function barangMasukDetail()
    {
        return $this->belongsTo(
            BarangMasukDetail::class,
            'barang_masuk_detail_id'
        );
    }

    public function detail(){
          return $this->belongsTo(BarangMasukDetail::class,
          'barang_masuk_detail_id');
    }
}
