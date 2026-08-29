<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\BarangMasukDetail;


class BarangMasuk extends Model
{

    protected $fillable = [

    'nomor_transaksi',
    'operator',
    'keterangan',
    'tanggal_masuk',


];


    public function details()
    {
        return $this->hasMany(BarangMasukDetail::class,
        'barang_masuk_id');
    }


    public static function generateNomor()
    {

        $tanggal = carbon::now()->format('Ymd');

        $last = self::whereDate('created_at', Carbon::today())
              ->latest()
              ->first();

        if(!$last){
            return 'BM'.$tanggal.'0001';
        }
            $urut = (int) substr($last->nomor_transaksi, -4);

            return 'BM'.$tanggal.str_pad($urut + 1, 4, '0', STR_PAD_LEFT);
    }



}
