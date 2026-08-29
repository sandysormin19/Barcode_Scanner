@extends('adminlte.page')

@section('title', 'Dashboard')

@section('css')

<link rel="stylesheet" href="{{ asset('css/wms.css') }}">

@endsection

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>Produksi</h4>
            <p>Selamat datang di Gudang.</p>
        </div>

        <div class="container-fluid">
            @include('dashboard.summary')
            Terakhir diperbarui:
            {{ now()->format('d M Y H:i') }}
        </div>

    </div>
@stop

@section('js')

<!-- Helpers dulu -->
<script src="{{ asset('js/modules/api-helper.js') }}"></script>
<script src="{{ asset('js/modules/table-helper.js') }}"></script>
<script src="{{ asset('js/modules/serial-helper.js') }}"></script>
<script src="{{ asset('js/modules/scanner-helper.js') }}"></script>

<!-- Main script -->
<script src="{{ asset('js/barang-masuk-scanner.js') }}"></script>
@stop
