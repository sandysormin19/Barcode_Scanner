@extends('adminlte::page')

@section('title', 'Barang Masuk')


@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1 class="m-0">
        Barang Masuk
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

<div class="card card-primary card-outline shadow">

    {{-- HEADER --}}
    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-box-open mr-2"></i>

            Riwayat Barang Masuk

        </h3>

    </div>


    {{-- BODY --}}
    <div class="card-body">


        {{-- PILIH JUMLAH DATA --}}
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <label class="mb-0 text-muted">
                    Tampilkan
                </label>

                <select
                    class="form-control form-control-sm d-inline-block ml-2"
                    style="width: 75px;"
                    onchange="window.location.href='?per_page=' + this.value"
                >

                    <option value="5"
                        {{ request('per_page', 10) == 5 ? 'selected' : '' }}>
                        5
                    </option>

                    <option value="10"
                        {{ request('per_page', 10) == 10 ? 'selected' : '' }}>
                        10
                    </option>

                    <option value="25"
                        {{ request('per_page', 10) == 25 ? 'selected' : '' }}>
                        25
                    </option>

                    <option value="50"
                        {{ request('per_page', 10) == 50 ? 'selected' : '' }}>
                        50
                    </option>

                </select>

                <span class="text-muted ml-1">
                    data per halaman
                </span>

            </div>

        </div>


        {{-- TABLE --}}
        <div class="table-responsive">

            <table class="table table-bordered table-hover">

                <thead class="bg-primary text-white">

                    <tr>

                        <th width="60" class="text-center">
                            No.
                        </th>

                        <th>
                            Nomor Transaksi
                        </th>

                        <th width="150">
                            Tanggal
                        </th>

                        <th>
                            Operator
                        </th>

                        <th width="120" class="text-center">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($barangMasuks as $index => $barangMasuk)

                    <tr>

                        {{-- NOMOR URUT --}}
                        <td class="text-center">

                            {{ $barangMasuks->firstItem() + $index }}

                        </td>


                        {{-- NOMOR TRANSAKSI --}}
                        <td>

                            <strong>
                                {{ $barangMasuk->nomor_transaksi }}
                            </strong>

                        </td>


                        {{-- TANGGAL --}}
                        <td>

                            {{ $barangMasuk->tanggal_masuk }}

                        </td>


                        {{-- OPERATOR --}}
                        <td>

                            {{ $barangMasuk->operator ?? '-' }}

                        </td>


                        {{-- AKSI --}}
                        <td class="text-center">

                            <a
                                href="{{ route(
                                    'barang-masuk.show',
                                    $barangMasuk->id
                                ) }}"
                                class="btn btn-info btn-sm"
                            >

                                <i class="fas fa-eye mr-1"></i>

                                Detail

                            </a>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="5"
                            class="text-center text-muted py-5"
                        >

                            <i class="fas fa-box-open fa-2x mb-2"></i>

                            <br>

                            Belum ada transaksi barang masuk.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if($barangMasuks->total() > 0)

        <div class="row align-items-center mt-3">


            {{-- INFORMASI DATA --}}
            <div class="col-md-6 mb-2 mb-md-0">

                <small class="text-muted">

                    Menampilkan

                    <strong>
                        {{ $barangMasuks->firstItem() }}
                    </strong>

                    -

                    <strong>
                        {{ $barangMasuks->lastItem() }}
                    </strong>

                    dari

                    <strong>
                        {{ $barangMasuks->total() }}
                    </strong>

                    data

                </small>

            </div>


            {{-- NAVIGASI PAGINATION --}}
            <div class="col-md-6">

                <div class="d-flex justify-content-md-end">

                    {{ $barangMasuks->onEachSide(1)->links() }}

                </div>

            </div>


        </div>

        @endif


    </div>

</div>

@stop
