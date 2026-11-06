@extends('adminlte.page')

@section('title', 'Detail Barang Masuk')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Detail Barang Masuk</h1>
        <a href="{{ route('barang-masuk.scanner') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-barcode mr-1"></i>
            Scanner
        </a>
    </div>
@stop

@section('content')
    <div class="card card-default shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="text-muted small">Nomor Transaksi</div>
                    <div class="font-weight-bold">{{ $barangMasuk->nomor_transaksi }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="text-muted small">Tanggal</div>
                    <div class="font-weight-bold">{{ $barangMasuk->tanggal_masuk }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="text-muted small">Operator</div>
                    <div class="font-weight-bold">{{ $barangMasuk->operator ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-default shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Qty</th>
                            <th>Batch</th>
                            <th>Serial</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangMasuk->details as $index => $detail)
                            @php
                                $serialText = $detail->serials->pluck('serial_number')->implode(', ');
                                $serialTotal = $detail->serials->sum('qty');
                            @endphp

                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detail->barang?->kode_tampilan ?? '-' }}</td>
                                <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                                <td>{{ $detail->qty }}</td>
                                <td>{{ $detail->batch ?? '-' }}</td>
                                <td>
                                    @if($detail->serials->isNotEmpty())
                                        <div class="text-break">{{ $serialText }}</div>
                                        @if($serialTotal == $detail->qty)
                                            <small class="text-success">Serial sesuai qty</small>
                                        @else
                                            <small class="text-danger">Serial tidak sesuai qty</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Tidak ada detail barang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
