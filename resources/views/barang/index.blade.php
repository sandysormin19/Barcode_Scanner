@extends('adminlte.page')

@section('title', 'Master Barang')

@section('content_header')
    <h1>Master Barang</h1>
@stop

@section('content')

<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <h4 class="card-title mb-0">
            Data Barang
        </h4>

        <button
            type="button"
            class="btn btn-success"
            data-toggle="modal"
            data-target="#modalImport">

            <i class="fas fa-file-excel"></i>
            Import Excel

        </button>

    </div>

    <div class="card-body">

<form action="{{ route('barang.cetak-barcode') }}" method="POST">

@csrf

<button
    type="submit"
    class="btn btn-warning mb-3">

    <i class="fas fa-barcode"></i>
    Cetak Barcode Terpilih

</button>
        <table class="table table-bordered">
            <thead>
                <tr>
<th width="40">
        <input type="checkbox" id="checkAll">
    </th>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Unit</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($barangs as $barang)
                    <tr>
<td>

<input
        type="checkbox"
        name="barang_id[]"
        value="{{ $barang->id }}"
        class="checkBarang">

</td>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $barang->kode_barang }}</td>
                        <td>{{ $barang->nama_barang }}</td>
                        <td>{{ $barang->unit }}</td>
                        <td>
        <a href="{{ route('barang.show', $barang->id) }}"
           class="btn btn-info btn-sm">
            <i class="fas fa-eye"></i> Detail
        </a>
        <a href="{{ route('barang.barcode',$barang->id) }}"
class="btn btn-warning btn-sm">

<i class="fas fa-barcode"></i>

Barcode

</a>
    </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            Belum ada data barang.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
</form>
    </div>

</div>

@include('barang.modal-import')

@stop

@section('js')

<script src="{{ asset('js/modules/api-helper.js') }}"></script>
<script src="{{ asset('js/modules/table-helper.js') }}"></script>
<script src="{{ asset('js/modules/serial-helper.js') }}"></script>
<script src="{{ asset('js/modules/scanner-helper.js') }}"></script>

<script src="{{ asset('js/barang-masuk-scanner.js') }}"></script>
<script>

$('#checkAll').click(function(){

    $('.checkBarang')
        .prop(
            'checked',
            this.checked
        );
});
</script>
@stop
