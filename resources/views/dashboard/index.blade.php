@extends('adminlte.page')

@section('title', 'Dashboard')

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
