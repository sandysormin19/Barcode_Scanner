<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Barang Keluar</title>
    <link rel="stylesheet" href="{{ asset('css/cetak.css') }}">
</head>

<body>
    <div class="no-print" style="margin-bottom:20px;">
        <button onclick="window.print()">Cetak</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <div class="header">
        <h2>LAPORAN BARANG KELUAR</h2>
        <p>
            Tanggal:
            {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nomor Transaksi</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Unit</th>
                <th>Qty</th>
                <th>Batch</th>
                <th>Operator</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($data as $row)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">
                        {{ $row->barangKeluar ? \Carbon\Carbon::parse($row->barangKeluar->tanggal)->format('d-m-Y') : '-' }}
                    </td>
                    <td>{{ $row->barangKeluar?->nomor_transaksi ?? '-' }}</td>
                    <td>{{ $row->barang?->kode_tampilan ?? '-' }}</td>
                    <td>{{ $row->barang?->nama_barang ?? '-' }}</td>
                    <td class="text-center">{{ $row->barang?->unit ?? '-' }}</td>
                    <td class="text-center">{{ $row->qty ?? 0 }}</td>
                    <td>{{ $row->batch ?? '-' }}</td>
                    <td>{{ $row->barangKeluar?->operator ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">
                        Tidak ada Barang Keluar hari ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>
            Dicetak pada:
            {{ now()->format('d-m-Y H:i') }}
        </p>
    </div>
</body>

</html>
