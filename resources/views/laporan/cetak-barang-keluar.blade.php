<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Laporan Barang Masuk</title>

    <link rel="stylesheet" href="{{ asset('css/cetak.css') }}">

</head>

<body>
    <div class="no-print" style="margin-bottom:20px;">
        <button onclick="window.print()">
            Cetak
        </button>
         <button onclick="window.close()">
            Tutup
        </button>
    </div>

    <div class="header">
        <h2>LAPORAN BARANG KELUAR</h2>
        <P>
            Tanggal:
            {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}
        </P>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggak</th>
                <th>Nomor transaksi</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Unit</th>
                <th>Qty</th>
                <th>Batch</th>
                <th>Operator</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($data as $row )
            <tr>
                <td class="text-center">
                    {{$loop->iteration}}
                </td>
                <td class="text-center">
                    {{\Carbon\Carbon::parse(
                        $row->barangMasuk->tanggal_masuk
                    )->format('d-m-Y')}}
                </td>
                <td>{{$row->barangMasuk->nomor_transaksi}}</td>
                 <td>
                    {{ $row->barang->kode_barang }}
                </td>

                <td>
                    {{ $row->barang->nama_barang }}
                </td>

                <td class="text-center">
                    {{ $row->barang->unit }}
                </td>

                <td class="text-center">
                    {{ $row->qty }}
                </td>

                <td>
                    {{ $row->batch ?? '-' }}
                </td>

                <td>
                    {{ $row->barangMasuk->operator ?? '-' }}
                </td>

            </tr>

            @empty
            <tr>
                <td colspan="9" class="text-center">
                    Tidak ada Barang keluar hari ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>
            Dicetak pada:
            {{now()->format('d-m-Y H:i')}}
        </p>
    </div>
</body>

</html>
