
@extends('adminlte.page')

@section('title', 'Barang Keluar')

@section('content_header')

<div class="d-flex justify-content-between align-items-center flex-wrap mb-4">

    <div>
        <div class="d-flex align-items-center mb-2">
            <div class="page-icon mr-3">
                <i class="fas fa-dolly"></i>
            </div>

            <div>
                <h1 class="page-title mb-0">
                    Barang Keluar
                </h1>

                <p class="page-subtitle mb-0">
                    Kelola dan pantau riwayat transaksi barang keluar
                </p>
            </div>
        </div>
    </div>

    <a href="{{ route('barang-keluar.scanner') }}"
       class="btn btn-primary btn-scanner shadow-sm mt-2 mt-md-0">

        <i class="fas fa-barcode mr-2"></i>
        Barang Keluar Scanner

    </a>

</div>

@stop


@section('content')

<div class="card transaction-card">

    <div class="card-header transaction-header">

        <div class="d-flex justify-content-between align-items-center">

            <div class="d-flex align-items-center">

                <div class="header-icon mr-3">
                    <i class="fas fa-clipboard-list"></i>
                </div>

                <div>
                    <h3 class="transaction-title mb-1">
                        Riwayat Barang Keluar
                    </h3>

                    <p class="transaction-subtitle mb-0">
                        Daftar seluruh transaksi barang keluar
                    </p>
                </div>

            </div>

            <div class="transaction-label">
                <i class="fas fa-history mr-1"></i>
                Riwayat
            </div>

        </div>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table transaction-table mb-0">


<thead>

    <tr>

        <th class="text-center" width="75">
            No
        </th>

        <th width="330">
            Nomor Transaksi
        </th>

        <th>
            Tanggal Transaksi
        </th>

        <th class="text-center" width="250">
            Aksi
        </th>

    </tr>

</thead>

                <tbody>


@forelse ($barangKeluars as $index => $barangKeluar)

    <tr>

        {{-- Nomor --}}
        <td class="text-center">

            <span class="row-number">
                {{ $index + 1 }}
            </span>

        </td>


        {{-- Nomor Transaksi --}}
        <td>

            <div class="transaction-number">

                <div class="transaction-symbol">
                    <i class="fas fa-receipt"></i>
                </div>

                <div>

                    <span class="transaction-code">
                        {{ $barangKeluar->nomor_transaksi }}
                    </span>

                    <small class="transaction-type">
                        Barang Keluar
                    </small>

                </div>

            </div>

        </td>


        {{-- Tanggal --}}
        <td>

            <div class="date-wrapper">

                <div class="date-icon">
                    <i class="far fa-calendar-alt"></i>
                </div>

                <div>

                    <span class="date-value">
                        {{ $barangKeluar->tanggal }}
                    </span>

                    <small class="date-label">
                        Tanggal transaksi
                    </small>

                </div>

            </div>

        </td>


        {{-- Aksi --}}
        <td>

            <div class="action-buttons">

                <a href="{{ route('barang-keluar.show', $barangKeluar) }}"
                   class="btn-action btn-detail">

                    <i class="fas fa-eye"></i>
                    <span>Detail</span>

                </a>


                <a href="{{ route('barang-keluar.track', $barangKeluar) }}"
                   class="btn-action btn-track">

                    <i class="fas fa-map-marked-alt"></i>
                    <span>Tracking</span>

                </a>

            </div>

        </td>

    </tr>

@empty

    <tr>

        <td colspan="4">

            <div class="empty-state">

                <div class="empty-icon">
                    <i class="fas fa-dolly"></i>
                </div>

                <h5>
                    Belum Ada Transaksi
                </h5>

                <p>
                    Belum ada transaksi barang keluar.
                </p>

            </div>

        </td>

    </tr>

@endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@stop


@section('css')

<link rel="stylesheet"
      href="{{ asset('css/barang-keluar.css') }}">

@stop
