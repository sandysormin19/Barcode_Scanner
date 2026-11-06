@extends('adminlte.page')

@section('title', 'Stok Barang')

@section('content_header')
    <h1>Stok Barang</h1>
@stop

@section('content')
<div class="mb-3 text-muted">
    <strong>Total:</strong> {{ $ringkasan->total_barang ?? 0 }}
    <span class="mx-2">|</span>
    <strong>Habis:</strong> {{ $ringkasan->stok_habis ?? 0 }}
    <span class="mx-2">|</span>
    <strong>Menipis (1–{{ $batasMenipis }}):</strong> {{ $ringkasan->stok_menipis ?? 0 }}
    <span class="mx-2">|</span>
    <strong>Aman:</strong> {{ $ringkasan->stok_aman ?? 0 }}
</div>
<div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('stok.index') }}" class="form-row align-items-end mb-3">
            <div class="form-group col-md-6">
                <label for="search">Cari barang</label>
                <input id="search" type="search" name="search" class="form-control"
                       value="{{ $search }}" placeholder="Kode barang atau nama barang">
            </div>
            <div class="form-group col-md-3">
                <label for="status">Status stok</label>
                <select id="status" name="status" class="form-control">
                    <option value="" {{ $status === '' ? 'selected' : '' }}>Semua status</option>
                    <option value="habis" {{ $status === 'habis' ? 'selected' : '' }}>Stok habis</option>
                    <option value="menipis" {{ $status === 'menipis' ? 'selected' : '' }}>Stok menipis</option>
                    <option value="aman" {{ $status === 'aman' ? 'selected' : '' }}>Stok aman</option>
                </select>
            </div>
            <div class="form-group col-md-2">
                <label for="per_page">Per halaman</label>
                <select id="per_page" name="per_page" class="form-control">
                    @foreach ([10, 25, 50, 100] as $jumlah)
                        <option value="{{ $jumlah }}" {{ $perPage === $jumlah ? 'selected' : '' }}>{{ $jumlah }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-1">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Unit</th>
                        <th class="text-center">Stok</th>
                        <th>Status</th>
                        <th>Terakhir Update</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stok as $item)
                        @php
                            $jumlahStok = (int) ($item->stok?->stok ?? 0);
                            $stokHabis = $jumlahStok <= 0;
                            $stokMenipis = $jumlahStok > 0 && $jumlahStok <= $batasMenipis;
                        @endphp
                        <tr class="{{ $stokHabis ? 'table-danger' : ($stokMenipis ? 'table-warning' : '') }}">
                            <td>{{ $stok->firstItem() + $loop->index }}</td>
                            <td>{{ $item->kode_tampilan }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td>{{ $item->unit }}</td>
                            <td class="text-center"><strong>{{ $jumlahStok }}</strong></td>
                            <td>
                                @if ($stokHabis)
                                    <span class="badge badge-danger">Habis</span>
                                @elseif ($stokMenipis)
                                    <span class="badge badge-warning">Menipis</span>
                                @else
                                    <span class="badge badge-success">Aman</span>
                                @endif
                            </td>
                            <td>{{ $item->stok?->updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td>
                                <a href="{{ route('barang.show', $item->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center">Barang tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex justify-content-end">{{ $stok->links() }}</div>
        <small class="text-muted">Stok menipis ditetapkan otomatis pada jumlah {{ $batasMenipis }} unit atau kurang.</small>
    </div>
</div>
@stop
