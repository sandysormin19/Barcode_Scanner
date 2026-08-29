<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kategori;

class Barang extends Model
{
    protected $fillable = [
        'kategori_id',
        'kode_barang',
        'nama_barang',
        'unit',
        'barang_induk',
        'brand',
        'item_sablon',
        'proses',
        'handfeel',
        'pakai_no_seri',
        'paksa_no_seri',
        'tipe_seri_batch',
        'tipe_barang',
        'departemen',
        'is_active',

    ];
    public function kategori(): BelongsTo
{
    return $this->belongsTo(Kategori::class);
}

    public function stok()
    {
        return $this->hasOne(StokBarang::class);
    }
}
