@extends('adminlte::page')

@section('title', 'Detail Barang')

@section('content_header')
<h1>Detail Barang</h1>
@stop

@section('content')

<div class="card">

    <div class="card-header">

        <h4>{{ $barang->kode_tampilan }} — {{ $barang->item_barang }}</h4>

    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <tr>
                <th width="250">Kode Barang</th>
                <td>{{ $barang->kode_tampilan }}</td>
            </tr>

            <tr>
                <th>Item Barang</th>
                <td>{{ $barang->item_barang }}</td>
            </tr>

            <tr>
                <th>Nama Barang</th>
                <td>{{ $barang->nama_barang }}</td>
            </tr>

            <tr>
                <th>Kategori</th>
                <td>{{ optional($barang->kategori)->nama_kategori }}</td>
            </tr>

            <tr>
                <th>Unit</th>
                <td>{{ $barang->unit }}</td>
            </tr>

            <tr>
                <th>Barang Induk</th>
                <td>{{ $barang->barang_induk }}</td>
            </tr>

            <tr>
                <th>Brand</th>
                <td>{{ $barang->brand }}</td>
            </tr>

            <tr>
                <th>Item Sablon</th>
                <td>{{ $barang->item_sablon }}</td>
            </tr>

            <tr>
                <th>Proses</th>
                <td>{{ $barang->proses }}</td>
            </tr>

            <tr>
                <th>Handfeel</th>
                <td>{{ $barang->handfeel }}</td>
            </tr>

            <tr>
                <th>Pakai No Seri</th>
                <td>{{ $barang->pakai_no_seri ? 'Ya' : 'Tidak' }}</td>
            </tr>

            <tr>
                <th>Paksa No Seri</th>
                <td>{{ $barang->paksa_no_seri ? 'Ya' : 'Tidak' }}</td>
            </tr>

            <tr>
                <th>Tipe Seri / Batch</th>
                <td>{{ $barang->tipe_seri_batch }}</td>
            </tr>

            <tr>
                <th>Tipe Barang</th>
                <td>{{ $barang->tipe_barang }}</td>
            </tr>

            <tr>
                <th>Departemen</th>
                <td>{{ $barang->departemen }}</td>
            </tr>

        </table>

    </div>

</div>

@stop
