@extends('adminlte.page')

@section('title', 'Detail Barang Keluar')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Detail Barang Keluar</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('barang-keluar.scanner') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-barcode mr-1"></i>
                Scanner
            </a>
            <a href="{{ route('barang-keluar.track', $barangKeluar) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-map-marked-alt mr-1"></i>
                Tracking
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-default shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="text-muted small">Nomor Transaksi</div>
                    <div class="font-weight-bold">{{ $barangKeluar->nomor_transaksi }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="text-muted small">Status</div>
                    @if($barangKeluar->status)
                        <span class="badge badge-{{ $barangKeluar->status->badgeColor() }}">
                            {{ $barangKeluar->status->label() }}
                        </span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>

                <div class="col-md-4 mb-3">
                    <div class="text-muted small">Tanggal</div>
                    <div class="font-weight-bold">{{ $barangKeluar->tanggal }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="text-muted small">Kurir Internal</div>
                    <div class="font-weight-bold">{{ $barangKeluar->courier ?? '-' }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="text-muted small">Tujuan</div>
                    <div class="font-weight-bold">{{ $barangKeluar->destination ?? '-' }}</div>
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
                            <th width="40">No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Qty</th>
                            <th>Batch</th>
                            <th>Serial</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangKeluar->details as $index => $detail)
                            @php
                                $serialText = $detail->serials->pluck('serial_number')->implode(', ');
                                $serialTotal = $detail->serials->sum('qty');
                            @endphp

                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detail->barang?->kode_tampilan ?? '-' }}</td>
                                <td>{{ $detail->barang?->nama_barang ?? '-' }}</td>
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
