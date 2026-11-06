@extends('adminlte::page')

@section('title','Label Barcode')


@section('content')

<div class="card">

    <div class="card-header">

        <h3 class="card-title">
            Label Barcode
        </h3>

    </div>


    <div class="card-body">


        <div class="barcode-label">

            <img
            src="data:image/png;base64,{{ $barcode }}"
            class="barcode-image">


            <p>
                {{ $barang->kode_tampilan }}
            </p>

        </div>


        <br>


        <button
        onclick="window.print()"
        class="btn btn-success">

            <i class="fas fa-print"></i>
            Cetak Label

        </button>


    </div>

</div>


@section('css')
    <link rel="stylesheet" href="{{ asset('css/barcode.css') }}">
@stop
@stop



