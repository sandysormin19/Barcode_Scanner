<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarSerial;



class BarangKeluarDetail extends Model
{
    protected $fillable = [
        'barang_keluar_id',
        'barang_id',
        'qty',
        'batch',
        'serial_number',
    ];

    public function barangKeluar()
    {
        return $this->belongsTo(
            BarangKeluar::class,
            'barang_keluar_id'
        );
    }

    public function barang()
    {
        return $this->belongsTo(
            Barang::class,
            'barang_id'
        );
    }

   public function serials ()
   {
    return $this->hasMany(
        BarangKeluarSerial::class,
        'barang_keluar_detail_id'
    );
   }
}
