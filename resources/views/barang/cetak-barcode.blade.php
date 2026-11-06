@extends('adminlte::page')

@section('title', 'Cetak Barcode')

@section('content')
    <div class="mb-3 no-print">
        <button
            type="button"
            onclick="window.print()"
            class="btn btn-success">
            <i class="fas fa-print"></i>
            Cetak Barcode
        </button>

        <a href="{{ route('barang.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>

   <div class="label-sheet">
    @forelse ($labels as $label)
        <div class="barcode-label">
            <div class="label-header">
                <div class="label-batch">{{ $label['batch'] }}</div>
                <div class="label-serial">{{ $label['serial_number'] }}</div>
            </div>
            <div class="label-item">
                {{ $label['item_barang'] }}
                @if ($label['size'] !== '')
                    {{ $label['size'] }}
                @endif
            </div>

            <img
                src="data:image/png;base64,{{ $label['barcode'] }}"
                class="barcode-image"
                alt="Barcode {{ $label['barcode_value'] }}">
        </div>
    @empty
        <div class="alert alert-warning no-print">
            Tidak ada label barcode yang dapat dibuat.
        </div>
    @endforelse
</div>
@stop

@section('js')
<script>
localStorage.removeItem('wms-barang-barcode-selection');
localStorage.removeItem('wms-barang-barcode-quantities');
</script>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/barcode.css') }}">
@stop
