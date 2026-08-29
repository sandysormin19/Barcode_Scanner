
@extends('adminlte::page')

@section('title', 'Cetak Barcode')

@section('content')

<div class="mb-3 no-print">

    <button
        onclick="window.print()"
        class="btn btn-success">

        <i class="fas fa-print"></i>
        Cetak Barcode

    </button>

</div>

<div class="label-sheet">

    @foreach($barangs as $barang)

        <div class="barcode-label">

            <img
                src="data:image/png;base64,{{ $barang->barcode }}"
                class="barcode-image"
                alt="{{ $barang->kode_barang }}">

            <div class="barcode-code">
                {{ $barang->kode_barang }}
            </div>

        </div>

    @endforeach

</div>

@section('css')
    <link rel="stylesheet" href="{{ asset('css/barcode.css') }}">
@stop
@stop

