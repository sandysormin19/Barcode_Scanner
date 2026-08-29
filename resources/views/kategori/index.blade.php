@extends('adminlte.page')

@section('title', 'Master Kategori')

@section('content_header')
    <h1>Master Kategori</h1>


@section('content')
<div class="card">
    <div class="card-header">

    <h4 class="card-title">
        Data Kategori
    </h4>

    <div class="card-tools">

        <button
            class="btn btn-primary"
            data-toggle="modal"
            data-target="#modalCreate">
            <i class="fas fa-plus"></i>

            Tambah

        </button>

    </div>

</div>

    <div class="card-body">
        <table class="table table-bordered">
    <thead>
        <tr>
            <th width="80">No</th>
            <th>Nama Kategori</th>
            <th>Deskripsi</th>
            <th width="120">Status</th>
        </tr>
    </thead>

    <tbody>

        @forelse ($kategoris as $kategori)

            <tr>

                <td>{{ $loop->iteration }}</td>

                <td>{{ $kategori->nama_kategori }}</td>

                <td>{{ $kategori->deskripsi }}</td>

                <td>

                    @if($kategori->is_active)

                        Aktif

                    @else

                        Tidak Aktif

                    @endif

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="4" class="text-center">

                    Data kategori belum tersedia.

                </td>

            </tr>

        @endforelse

    </tbody>

</table>
    </div>
</div>
@endsection

@stop
@include('kategori.modal-create')
