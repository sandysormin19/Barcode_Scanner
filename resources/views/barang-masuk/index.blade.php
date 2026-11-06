@extends('adminlte.page')

@section('title', 'Barang Masuk')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Barang Masuk</h1>
        <a href="{{ route('barang-masuk.scanner') }}" class="btn btn-primary btn-sm">
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
                            <th>Operator</th>
                            <th width="90">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangMasuks as $index => $barangMasuk)
                            <tr>
                                <td>{{ $barangMasuks->firstItem() + $index }}</td>
                                <td>{{ $barangMasuk->nomor_transaksi }}</td>
                                <td>{{ $barangMasuk->tanggal_masuk }}</td>
                                <td>{{ $barangMasuk->operator ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('barang-masuk.show', $barangMasuk->id) }}" class="btn btn-info btn-xs">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada transaksi barang masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
