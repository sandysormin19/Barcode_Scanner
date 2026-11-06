<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokBarang extends Model
{
    protected $fillable = [
        'barang_id',
        'stok',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function stokBarang()
    {
        return $this->hasOne(StokBarang::class, 'barang_id');
    }
}
