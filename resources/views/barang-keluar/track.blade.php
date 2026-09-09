@extends('adminlte.page')

@section('title', 'Tracking Barang Keluar')

@push('css')
    @vite(['resources/css/tracker.css'])
@endpush

@section('content')
    <div class="tracker-shell">
        <div class="tracker-summary">
            <div class="tracker-summary__header">
                <h4 class="mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-boxes"></i>
                    Tracking Pengiriman
                </h4>
            </div>

            <div class="tracker-summary__body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="small text-muted mb-1">Nomor Transaksi</div>
                        <div class="fw-bold">{{ $barangKeluar->nomor_transaksi }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted mb-1">Kurir Internal</div>
                        <div class="fw-bold">{{ $barangKeluar->courier ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted mb-1">Status Saat Ini</div>
                        @php $currentStatus = $barangKeluar->status; @endphp
                        <span class="tracker-status-pill tracker-status-pill--{{ $currentStatus->value }}">
                            <i class="fas {{ $currentStatus->icon() }}"></i>
                            {{ $currentStatus->label() }}
                        </span>
                    </div>
                </div>

                <div class="row g-3 mt-2">
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

        <div class="mt-4">
            <div class="tracker-summary">
                <div class="tracker-summary__header">
                    <h5 class="mb-0">Riwayat Status</h5>
                </div>

                <div class="tracker-summary__body">
                    @if (empty($timeline))
                        <div class="tracker-empty">
                            Belum ada riwayat status untuk pengiriman ini.
                        </div>
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
                                            <div class="tracker-item__text">
                                                {{ $item['keterangan'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if (in_array($barangKeluar->status, [\App\Enums\StatusPengiriman::Pending, \App\Enums\StatusPengiriman::Packed, \App\Enums\StatusPengiriman::InTransit], true))
            <div class="mt-4 tracker-form">
                <h5 class="mb-3">Update Status Pengiriman</h5>

                <form id="status-form" action="{{ route('barang-keluar.update-status', $barangKeluar) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="">Pilih status</option>
                            @foreach ($barangKeluar->status->nextValidStatuses() as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="form-control" placeholder="Tambahkan keterangan pengiriman"></textarea>
                    </div>

                    <div class="tracker-form__actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
@endsection

@push('js')
    @vite(['resources/js/tracker.js'])
@endpush
