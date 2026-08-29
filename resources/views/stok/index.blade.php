@extends('adminlte.page')

@section('title', 'Stok Barang')

@section('content_header')
<h1>Stok Barang</h1>
@stop

@section('content')

<div class="card">

    <div class="card-body">

        <table class="table table-bordered table-striped">

            <thead class="bg-primary">

                <tr>

                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Unit</th>
                    <th class="text-center">Stok</th>
                    <th>Dibuat</th>
                    <th>Terakhir Update</th>

                </tr>

            </thead>

            <tbody>

                @foreach($stok as $item)

                <tr>

                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->barang->kode_barang }}</td>
                    <td>{{ $item->barang->nama_barang }}</td>
                    <td>{{ $item->barang->unit }}</td>
                    <td class="text-center">
                        <span class="badge badge-success" >{{ $item->stok }}
                            </span>
                    </td>
                    <td>
                        {{ $item->created_at->format('d/m/Y H:i') }}
                    </td>

                    <td>
                        {{ $item->updated_at->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
