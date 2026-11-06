@extends('adminlte.page')

@section('title', 'Master Barang')

@section('content_header')
    <h1>Master Barang</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Data Barang</h4>
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalImport">
            <i class="fas fa-file-excel"></i> Import Excel
        </button>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('barang.index') }}" class="mb-3">
    <div class="input-group">
        <input
            type="search"
            name="search"
            class="form-control"
            placeholder="Cari no. barang, item, atau deskripsi..."
            value="{{ $search ?? request('search') }}"
        >
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Cari
        </button>

        @if (!empty($search))
            <a href="{{ route('barang.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </div>
</form>
        <form id="formCetakBarcode" action="{{ route('barang.cetak-barcode') }}" method="POST">
            @csrf
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Periksa kembali data barcode:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <button type="submit" class="btn btn-warning mb-3"><i class="fas fa-barcode"></i> Cetak Barcode Terpilih</button>
            <button type="button" id="clearBarcodeSelection" class="btn btn-outline-secondary mb-3">Kosongkan Pilihan</button>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th width="40"><input type="checkbox" id="checkAll"></th><th>No</th><th>No. Barang</th><th>Item Barang</th><th>Deskripsi Barang</th><th>Unit</th><th>Nomor Seri/Batch</th><th>Jumlah Barcode</th><th>Aksi</th></tr></thead>
                    <tbody>
                    @forelse ($barangs as $barang)
                        <tr>
                            <td><input type="checkbox" value="{{ $barang->id }}" class="checkBarang"></td>
                            <td>{{ $barangs->firstItem() ? $barangs->firstItem() + $loop->index : $loop->iteration }}</td>
                            <td>{{ $barang->kode_tampilan }}</td>
                            <td>{{ $barang->item_barang ?? '-' }}</td>
                            <td>{{ $barang->nama_barang }}</td>
                            <td>{{ $barang->unit }}</td>
                            <td>
                                @php
                                    $storedSerial = $barang->barangMasukDetails
                                        ->flatMap(fn ($detail) => $detail->serials)
                                        ->sortByDesc('id')
                                        ->first();
                                @endphp
                                {{ $storedSerial?->serial_number ?? '-' }}
                            </td>
                            <td>
                                <input type="number"
                                    class="form-control jumlahBarcode"
                                    data-barang-id="{{ $barang->id }}"
                                    min="1"
                                    max="500"
                                    value="1"
                                    aria-label="Jumlah barcode untuk {{ $barang->barang_induk }} / {{ $barang->item_barang }}">
                            </td>
                            <td><a href="{{ route('barang.show', $barang->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center">Belum ada data barang.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <div class="mt-3 d-flex justify-content-end">{{ $barangs->appends(request()->query())->links() }}</div>
    </div>
</div>

@include('barang.modal-import')
@stop

@section('js')
<script>
(() => {
    const selectionKey = 'wms-barang-barcode-selection';
    const quantityKey = 'wms-barang-barcode-quantities';
    const form = document.getElementById('formCetakBarcode');
    const checkboxes = [...document.querySelectorAll('.checkBarang')];
    const quantityInputs = [...document.querySelectorAll('.jumlahBarcode')];
    const checkAll = document.getElementById('checkAll');

    const readJson = (key, fallback) => {
        try {
            return JSON.parse(localStorage.getItem(key) || JSON.stringify(fallback));
        } catch (error) {
            return fallback;
        }
    };

    const navigation = performance.getEntriesByType('navigation')[0];
    if (navigation?.type === 'reload') {
        localStorage.removeItem(selectionKey);
        localStorage.removeItem(quantityKey);
    }

    let selected = new Set(readJson(selectionKey, []).map(String));
    let quantities = readJson(quantityKey, {});

    const updateCheckAll = () => {
        const checked = checkboxes.filter((checkbox) => checkbox.checked).length;
        checkAll.checked = checkboxes.length > 0 && checked === checkboxes.length;
        checkAll.indeterminate = checked > 0 && checked < checkboxes.length;
    };

    const saveSelection = () => {
        localStorage.setItem(selectionKey, JSON.stringify([...selected]));
        updateCheckAll();
    };

    const saveQuantities = () => {
        localStorage.setItem(quantityKey, JSON.stringify(quantities));
    };

    const restorePage = () => {
        selected = new Set(readJson(selectionKey, []).map(String));
        quantities = readJson(quantityKey, {});
        checkboxes.forEach((checkbox) => {
            checkbox.checked = selected.has(checkbox.value);
        });
        quantityInputs.forEach((input) => {
            input.value = quantities[input.dataset.barangId] || '1';
        });
        updateCheckAll();
    };

    restorePage();

    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            if (checkbox.checked) {
                selected.add(checkbox.value);
            } else {
                selected.delete(checkbox.value);
            }
            saveSelection();
        });
    });

    quantityInputs.forEach((input) => {
        input.addEventListener('change', () => {
            const value = Math.max(1, Math.min(500, parseInt(input.value || '1', 10)));
            input.value = String(value);
            quantities[input.dataset.barangId] = value;
            saveQuantities();
        });
    });

    checkAll?.addEventListener('change', () => {
        checkboxes.forEach((checkbox) => {
            checkbox.checked = checkAll.checked;
            if (checkAll.checked) {
                selected.add(checkbox.value);
            } else {
                selected.delete(checkbox.value);
            }
        });
        saveSelection();
    });

    document.getElementById('clearBarcodeSelection')?.addEventListener('click', () => {
        selected.clear();
        quantities = {};
        localStorage.removeItem(selectionKey);
        localStorage.removeItem(quantityKey);
        quantityInputs.forEach((input) => input.value = '1');
        checkboxes.forEach((checkbox) => checkbox.checked = false);
        updateCheckAll();
    });

    form?.addEventListener('submit', (event) => {
        selected = new Set(readJson(selectionKey, []).map(String));
        if (selected.size === 0) {
            event.preventDefault();
            window.alert('Pilih minimal satu barang untuk dicetak.');
            return;
        }

        quantities = readJson(quantityKey, {});
        form.querySelectorAll('.selected-barang-hidden').forEach((input) => input.remove());

        selected.forEach((id) => {
            const barangInput = document.createElement('input');
            barangInput.type = 'hidden';
            barangInput.name = 'barang_id[]';
            barangInput.value = id;
            barangInput.className = 'selected-barang-hidden';
            form.appendChild(barangInput);

            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = 'jumlah_cetak[' + id + ']';
            quantityInput.value = quantities[id] || '1';
            quantityInput.className = 'selected-barang-hidden';
            form.appendChild(quantityInput);
        });
    });

    window.addEventListener('pageshow', restorePage);
})();
</script>
@stop
