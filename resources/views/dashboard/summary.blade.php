<div class="row">
<div class="col-lg-3 col-6">
    <a href="{{ route('barang.index') }}" class="text-decoration-none">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($totalBarang) }}</h3>
                <p>Total Barang</p>
            </div>

            <div class="icon">
                <i class="fas fa-box"></i>
            </div>
        </div>
    </a>
</div>

<div class="col-lg-3 col-6">
    <a href="{{route('stok.index')}}" class="text-decoration-none">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{number_format($totalStok)}}</h3>
                <p>Total Stok</p>
                <div class="icon">
                    <i class="fas fa-warehouse"></i>
                </div>
            </div>
        </div>
    </a>
</div>

<div class="col-lg-3 col-6">
    <a href="{{route('stok-mutasi.index')}}" class="text-decoration-none">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{number_format($barangMasukHariIni)}}</h3>
                <p>Barang Masuk Hari Ini</p>
                <div class="icon">
                    <i class="fas fa-arrow-down"></i>
                </div>
            </div>
        </div>
    </a>
</div>

<div class="col-lg-3 col-6">
    <a href="{{route('stok-mutasi.index')}}" class="text-decoration-none">
        <div class="small-box bg-info">
            <div class="inner">
            <h3>{{number_format($barangKeluarHariIni)}}</h3>
            <p>Barang Keluar Hari Ini</p>
            <div class="icon">
                <i class="fas fa-arrow-up"></i>
            </div>
        </div>
        </div>
    </a>
</div>

</div>
