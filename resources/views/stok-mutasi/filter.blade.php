<form method="GET" class="mb-3">

    <div class="row">

        <div class="col-12 col-md-2">
            <input
                type="date"
                name="tanggal_awal"
                class="form-control"
                value="{{ request('tanggal_awal') }}">
        </div>

        <div class="col-12 col-md-2">
            <input
                type="date"
                name="tanggal_akhir"
                class="form-control"
                value="{{ request('tanggal_akhir') }}">
        </div>

        <div class="col-12 col-md-2">
            <select
                name="jenis"
                class="form-control">

                <option value="">Semua Jenis</option>

                <option value="MASUK"
                    {{ request('jenis')=='MASUK' ? 'selected' : '' }}>
                    MASUK
                </option>

                <option value="KELUAR"
                    {{ request('jenis')=='KELUAR' ? 'selected' : '' }}>
                    KELUAR
                </option>

                <option value="OPNAME"
                    {{ request('jenis')=='OPNAME' ? 'selected' : '' }}>
                    OPNAME
                </option>

            </select>
        </div>

        <div class="col-12 col-md-3">
            <input
                type="text"
                name="kode_barang"
                class="form-control"
                placeholder="Kode Barang"
                value="{{ request('kode_barang') }}">
        </div>

        <div class="col-12 col-md-3">

            <button class="btn btn-primary">
                <i class="fas fa-search"></i>
                Cari
            </button>

            <a href="{{ route('stok-mutasi.index') }}"
               class="btn btn-secondary">
                Reset
            </a>

        </div>

    </div>

</form>
