@extends('adminlte.page')

@section('title', 'Detail Barang Masuk')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1 class="m-0">
        Detail Barang Masuk
    </h1>

    <a
        href="{{ route('barang-masuk.scanner') }}"
        class="btn btn-primary"
    >
        <i class="fas fa-barcode mr-1"></i>
        Barang Masuk Scanner
    </a>

</div>

@stop


@section('content')

{{-- INFORMASI TRANSAKSI --}}
<div class="card card-primary card-outline shadow">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-file-invoice mr-2"></i>

            Informasi Transaksi

        </h3>

    </div>


    <div class="card-body">

        <div class="row">

            <div class="col-md-4">

                <label>
                    Nomor Transaksi
                </label>

                <div class="form-control bg-light">

                    {{ $barangMasuk->nomor_transaksi }}

                </div>

            </div>


            <div class="col-md-4">

                <label>
                    Tanggal
                </label>

                <div class="form-control bg-light">

                    {{ $barangMasuk->tanggal_masuk }}

                </div>

            </div>


            <div class="col-md-4">

                <label>
                    Operator
                </label>

                <div class="form-control bg-light">

                    {{ $barangMasuk->operator ?? '-' }}

                </div>

            </div>

        </div>

    </div>

</div>


{{-- DETAIL BARANG --}}
@foreach($barangMasuk->details as $detail)

<div class="card card-primary card-outline shadow">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-box-open mr-2"></i>

            Detail Barang

        </h3>

    </div>


    <div class="card-body">

        <div class="row">

            <div class="col-md-6">

                <label>
                    Kode Barang
                </label>

                <div class="form-control bg-light">

                    {{ $detail->barang?->kode_tampilan ?? '-' }}

                </div>

            </div>

            <div class="col-md-6">

                <label>
                    Nama Barang
                </label>

                <div class="form-control bg-light">

                    {{ $detail->barang->nama_barang ?? '-' }}

                </div>

            </div>


            <div class="col-md-6 mt-3">

                <label>
                    Qty
                </label>

                <div class="form-control bg-light">

                    {{ $detail->qty }}

                </div>

            </div>


            <div class="col-md-6 mt-3">

                <label>
                    Batch
                </label>

                <div class="form-control bg-light">

                    {{ $detail->batch ?? '-' }}

                </div>

            </div>

        </div>


        {{-- SERIAL NUMBER --}}
        <hr class="my-4">


        <div class="d-flex justify-content-between align-items-center mb-3">

            <h5 class="mb-0">

                <i class="fas fa-barcode mr-2"></i>

                Serial Number

            </h5>

            <span class="badge badge-primary">

                {{ $detail->serials->count() }} Serial

            </span>

        </div>


        @if($detail->serials->count() > 0)

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead class="bg-primary text-white">

                        <tr>

                            <th width="70">
                                No.
                            </th>

                            <th>
                                Serial Number
                            </th>

                            <th width="150">
                                Qty
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($detail->serials as $index => $serial)

                            <tr>

                                <td>
                                    {{ $index + 1 }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $serial->serial_number }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $serial->qty }}
                                </td>

                            </tr>

                        @endforeach

                    </tbody>


                    <tfoot>

                        <tr>

                            <th
                                colspan="2"
                                class="text-right"
                            >
                                Total Serial
                            </th>

                            <th>

                                {{ $detail->serials->sum('qty') }}

                            </th>

                        </tr>

                    </tfoot>

                </table>

            </div>

        @else

            <div class="alert alert-secondary">

                <i class="fas fa-info-circle mr-1"></i>

                Tidak ada serial number.

            </div>

        @endif


        {{-- Validasi totalnya --}}
        @if(
            $detail->serials->sum('qty')
            ==
            $detail->qty
        )

            <div class="alert alert-success mt-3 mb-0">

                <i class="fas fa-check-circle mr-1"></i>

                Total serial sesuai dengan Qty barang.

            </div>

        @else

            <div class="alert alert-danger mt-3 mb-0">

                <i class="fas fa-exclamation-triangle mr-1"></i>

                Total serial tidak sesuai dengan Qty barang.

            </div>

        @endif

    </div>

</div>

@endforeach

@stop
