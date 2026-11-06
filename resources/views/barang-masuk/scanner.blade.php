@extends('adminlte.page')

@section('title', 'Barang Masuk')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Barang Masuk Produksi</h1>
</div>
@stop

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Transaksi belum tersimpan:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="card card-primary card-outline shadow">

    <div class="card-haeader">
        <h3 class="card-title">
            <i class="fas fa-box-open mr-2"></i>
            Transaksi Barang Masuk
        </h3>
    </div>

<form
    action="{{ route('barang-masuk.store') }}"
    method="POST">
    @csrf
    <div class="card-body">

        <div class="row">

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label>Nomor Transaksi</label>

                    <input
                    type="text"
                     class="form-control"
                         value="{{ $nomor }}"
                            readonly>

                    <input type="hidden"
                    name="nomor_transaksi"
                        value="{{$nomor}}">
                    <input type="hidden"
                        name="mode"
                        value="scanner">
                </div>
            </div>

                <div class="col-12 col-md-4">
                    <div class="form-group">
                    <label>Operator</label>
                    <input type="text"
                               class="form-control"
                               name="operator"
                               placeholder="Nama Operator"
                               required>
                    </div>
                </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date"
                        class="form-control"
                        name="tanggal_masuk"
                        value="{{ date('Y-m-d') }}">
                </div>
            </div>

        </div>

        <hr>

        <div class="form-group">

            <label>Cari Barang</label>

            <div class="card border-primary">

    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-barcode"></i>
            Scanner Barcode
        </h5>
    </div>

    <div class="card-body text-center">

        <input
            type="text"
            id="kode_barang"
            class="form-control form-control-lg text-center"
            autocomplete="off"
            autofocus
            spellcheck="false"
            placeholder="Silakan scan barcode...">


        <div
            class="mt-3 text-success"
            id="scannerStatus">

            <i class="fas fa-circle"></i>
            Scanner Ready

        </div>

    </div>

</div>

        </div>
        <div class="table-responsive mt-3">

        <table class="table table-bordered table-hover">

            <thead class="bg-primary text-white">
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Barang</th>
                    <th width="10%">Unit</th>
                    <th width="10%">Qty</th>
                    <th width="15%">Batch</th>
                    <th width="25%">Nomor Seri</th>
                    <th width="10%">Aksi</th>
                </tr>
            </thead>

            <tbody id="detailBarang">
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="fas fa-box-open fa-2x mb-2"></i>
                        <br>
                        Belum ada barang yang dipilih.
                    </td>
                </tr>
            </tbody>

        </table>

        <div class="text-right mt-3">
            <button
                type="submit"
                id="btnSimpan"
                class="btn btn-success">
                <i class="fas fa-save"></i>
                Simpan Transaksi
            </button>
</form>
        </div>

    </div>
    </div>

</div>

@stop

@section('js')

<script>
window.barangMasukConfig = {
    cariUrl: "{{ route('barang.cari') }}"
};
</script>

<!-- Helpers dulu -->
<script src="{{ asset('js/modules/api-helper.js') }}"></script>
<script src="{{ asset('js/modules/table-helper.js') }}"></script>
<script src="{{ asset('js/modules/serial-helper.js') }}?v={{ filemtime(public_path('js/modules/serial-helper.js')) }}"></script>
<script src="{{ asset('js/modules/scanner-helper.js') }}"></script>

<!-- Main script -->
<script src="{{ asset('js/barang-masuk-scanner.js') }}?v={{ filemtime(public_path('js/barang-masuk-scanner.js')) }}"></script>
@stop

