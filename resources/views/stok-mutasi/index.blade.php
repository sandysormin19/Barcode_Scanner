@extends('adminlte.page')

@section('title', 'Stok Mutasi')

@section('content_header') <h1>Stok Mutasi</h1>
@stop

@section('content')

<div class="container-fluid">

@include('stok-mutasi.filter')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-exchange-alt mr-2"></i>
            Data Stok Mutasi
        </h3>
    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-bordered table-striped table-hover mb-0">

                <thead class="bg-primary text-white">

                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nomor Transaksi</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Unit</th>
                        <th>Jenis</th>
                        <th>Qty</th>
                        <th>Batch</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse ($mutasis as $item)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $item->created_at->format('d-m-Y') }}
                            </td>

                            <td>
                                {{ $item->nomor_transaksi }}
                            </td>

                            <td>
                                {{ $item->barang?->kode_tampilan ?? '-' }}
                            </td>

                            <td>
                                {{ $item->barang->nama_barang }}
                            </td>

                            <td>
                                {{ $item->barang->unit }}
                            </td>

                            <td class="text-center">

                                @if ($item->jenis == 'MASUK')

                                    <span class="badge badge-success">
                                        MASUK
                                    </span>

                                @elseif ($item->jenis == 'KELUAR')

                                    <span class="badge badge-danger">
                                        KELUAR
                                    </span>

                                @else

                                    <span class="badge badge-warning">
                                        OPNAME
                                    </span>

                                @endif

                            </td>

                            <td class="text-center">

                                <span class="badge badge-primary">
                                    {{ $item->qty }}
                                </span>

                            </td>

                            <td>
                                {{ $item->batch ?? '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                                <br>
                                Belum ada data stok mutasi.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


</div>

@stop




