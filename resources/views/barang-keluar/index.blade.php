@extends('adminlte.page')

@section('title', 'Barang Keluar')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Barang Keluar</h1>
        <a href="{{ route('barang-keluar.scanner') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-barcode mr-1"></i>
            Scanner
        </a>
    </div>
@stop

@section('content')
    <div class="card card-default shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Nomor Transaksi</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barangKeluars as $index => $barangKeluar)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $barangKeluar->nomor_transaksi }}</td>
                                <td>{{ $barangKeluar->tanggal }}</td>
                                <td>
                                    @if($barangKeluar->status)
                                        <span class="badge badge-{{ $barangKeluar->status->badgeColor() }}">
                                            {{ $barangKeluar->status->label() }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('barang-keluar.show', $barangKeluar) }}" class="btn btn-info btn-xs">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada transaksi barang keluar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
