@extends('adminlte.page')

@section('title', 'Cek Status Barang Keluar')

@push('css')
    @vite(['resources/css/tracker.css'])
@endpush

@section('content')
    <div class="tracker-shell">
        <div class="tracker-form mb-4">
            <h4 class="mb-3">
                <i class="fas fa-search mr-2"></i>
                Cek Status Pengiriman
            </h4>

            <form method="GET" action="{{ route('barang-keluar.cek-status') }}">
                <div class="input-group input-group-lg">
                    <input
                        type="text"
                        name="nomor_transaksi"
                        class="form-control"
                        value="{{ $nomorTransaksi ?? '' }}"
                        placeholder="Masukkan nomor transaksi"
                        required
                    >
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search me-1"></i>
                        Cari
                    </button>
                </div>
            </form>
        </div>

        @if ($barangKeluar)
            <div class="tracker-summary mb-4">
                <div class="tracker-summary__header">
                    <h5 class="mb-0">Informasi Pengiriman</h5>
                </div>

                <div class="tracker-summary__body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="small text-muted mb-1">Nomor Transaksi</div>
                            <div class="fw-bold">{{ $barangKeluar->nomor_transaksi }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="small text-muted mb-1">Status Saat Ini</div>
                            <span class="tracker-status-pill tracker-status-pill--{{ $barangKeluar->status->value }}">
                                <i class="fas {{ $barangKeluar->status->icon() }}"></i>
                                {{ $barangKeluar->status->label() }}
                            </span>
                        </div>
                        <div class="col-md-4">
                            <div class="small text-muted mb-1">Kurir Internal</div>
                            <div>{{ $barangKeluar->courier ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted mb-1">Tujuan</div>
                            <div>{{ $barangKeluar->destination ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted mb-1">Terakhir Diperbarui</div>
                            <div>{{ $barangKeluar->status_updated_at?->format('d M Y H:i') ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tracker-summary">
                <div class="tracker-summary__header">
                    <h5 class="mb-0">Riwayat Status</h5>
                </div>

                <div class="tracker-summary__body">
                    @if (empty($timeline))
                        <div class="tracker-empty">Belum ada riwayat status.</div>
                    @else
                        <div class="tracker-timeline">
                            @foreach ($timeline as $item)
                                @php $status = \App\Enums\StatusPengiriman::from($item['status']); @endphp
                                <div class="tracker-item">
                                    <div class="tracker-item__dot tracker-item__dot--{{ $status->value }}">
                                        <i class="fas {{ $status->icon() }}"></i>
                                    </div>
                                    <div class="tracker-item__content">
                                        <h6 class="tracker-item__title">{{ $status->label() }}</h6>
                                        <div class="tracker-item__meta">
                                            {{ \Carbon\Carbon::parse($item['tanggal'])->format('d M Y, H:i') }}
                                            • Oleh {{ $item['user'] }}
                                        </div>
                                        @if (! empty($item['keterangan']))
                                            <div class="tracker-item__text">{{ $item['keterangan'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @elseif ($nomorTransaksi)
            <div class="alert alert-warning mt-3">
                Nomor transaksi <strong>{{ $nomorTransaksi }}</strong> tidak ditemukan.
            </div>
        @endif
    </div>
@endsection
