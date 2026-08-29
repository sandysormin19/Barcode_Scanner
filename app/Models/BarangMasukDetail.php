<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BarangMasukSerial;
class BarangMasukDetail extends Model
{

    protected $fillable = [
    'barang_masuk_id',
    'barang_id',
    'qty',
    'batch'
];


    public function barang()

    {
        return $this->belongsTo(Barang::class,
        'barang_id');
    }

    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class,
        'barang_masuk_id');
    }

    public function serials()
    {
        return $this->hasMany
         (BarangMasukSerial::class,
         'barang_masuk_detail_id');
    }
}
