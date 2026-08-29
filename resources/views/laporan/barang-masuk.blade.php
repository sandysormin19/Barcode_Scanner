@extends('adminlte.page')

@section('title', 'Laporan Barang Masuk')

@section('content_header')
    <h1>Laporan Barang Masuk Hari Ini</h1>
@stop

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-file-alt mr-2"></i>
            Laporan Barang Masuk
        </h3>

        <div class="card-tools">
            <a href="{{route('laporan.barang-masuk.cetak')}}"
               target="_blank"
               class="btn btn-primary">
               <i class="fas fa-print mr-1"></i>
                Cetak
            </a>
        </div>
    </div>

    <div class="card-body">

        <div class="mb-3">
            <strong>Tanggal:</strong>
            {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}
        </div>

        <div class="table-responsive">

            <table class="table table-bordered table-striped">

                <thead class="bg-primary text-white">

                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nomor Transaksi</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Unit</th>
                        <th>Qty</th>
                        <th>Batch</th>
                        <th>Operator</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($data as $row)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse(
                                    $row->barangMasuk->tanggal_masuk
                                )->format('d-m-Y') }}
                            </td>

                            <td>
                                {{ $row->barangMasuk->nomor_transaksi }}
                            </td>

                            <td>
                                {{ $row->barang->kode_barang }}
                            </td>

                            <td>
                                {{ $row->barang->nama_barang }}
                            </td>

                            <td>
                                {{ $row->barang->unit }}
                            </td>

                            <td>
                                {{ $row->qty }}
                            </td>

                            <td>
                                {{ $row->batch ?? '-' }}
                            </td>

                            <td>
                                {{ $row->barangMasuk->operator ?? '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="9" class="text-center">
                                Belum ada barang masuk hari ini.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@stop
