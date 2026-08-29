<?php

namespace App\Imports;
use App\Models\Kategori;
use App\Models\Barang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarangImport implements ToModel, WithHeadingRow
{

 public function model(array $row)
{
    $kategori = Kategori::firstOrCreate([
        'nama_kategori' => $row['nama_kategori_barang'],
    ]);

    return Barang::updateOrCreate(
        [
            'kode_barang' => $row['no_barang'],
        ],
        [
            'kategori_id' => $kategori->id,

            'nama_barang' => $row['deskripsi_barang'],

            'unit' => $row['unit'],

            'barang_induk' => $row['barang_induk'],

            'brand' => $row['brand_name1'],

            'item_sablon' => $row['item_sablon_3'],

            'proses' => $row['proses_5'],

            'handfeel' => $row['handfeel_6'],

            'pakai_no_seri' =>
                strtolower($row['pakai_no_seri']) == 'ya',

            'paksa_no_seri' =>
                strtolower($row['paksa_no_seri']) == 'ya',

            'tipe_seri_batch' =>
                $row['tipe_seri_batch'],

            'tipe_barang' =>
                $row['tipe_barang'],

            'departemen' =>
                $row['nama_dep_default'],
        ]
    );
}
}
