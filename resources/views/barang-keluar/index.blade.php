@extends('adminlte.page')

@section('title', 'Barang Keluar')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">
        Barang Keluar
    </h1>

    <a href="{{route('barang-keluar.scanner')}}"
    class="btn btn-primary">

    <i class="fas fa-barcode mr-1">
        Barang Keluar Scanner
    </i>
    </a>
</div>

@stop

@section('content')

<div class="card card-primary card-outline shadow">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas da-dolly mr-2"></i>
                Riwayat Barang Keluar
        </h3>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-primary text-white">
                    <tr>
                        <th width="60">
                            No
                        </th>
                        <th>Nomor Transaksi</th>
                        <th>Tanggal</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($barangKeluars as $index => $barangKeluar)
                        <tr>
                            <td>{{$index + 1}}
                            </td>
                            <td>
                                <strong>
                                    {{$barangKeluar->nomor_transaksi}}
                                </strong>
                            </td>
                            <td>
                        {{$barangKeluar->tanggal}}
                            </td>
                            <td>
                                {{--Nanti Menuju Detail--}}
                            <a href="{{route ('barang-keluar.show', $barangKeluar)}}"
                            class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i>
                                Detail
                            </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4"
                            class="text-center text-muted py-4">
                            <i class="fas fa-dolly fa-2x mb-2"></i>
                            <br>
                            Belum ada transaksi barang keluar
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
