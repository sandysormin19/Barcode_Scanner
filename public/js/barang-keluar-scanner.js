

$(document).ready(function () {
    // Initialize
    ScannerHelper.focus();
    ScannerHelper.autoFocus();

    // Event: Scan barcode (Enter)
    $(document).on("keydown", "#kode_barang", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            scanBarcode();
        }
    });

    // Event: Delete barang
    $(document).on("click", ".btnHapus", function () {
        TableHelper.deleteRow($(this).closest("tr"));
        TableHelper.updateRowNumbers();
    });

    // Event: Hapus serial
    $(document).on("click", ".btnHapusSerial", function () {
        SerialHelper.deleteRow($(this));
    });

    // Event: Input qty serial
    $(document).on("input", ".serialQty", function () {
        const row = $(this).closest("tr");
        SerialHelper.calculateTotal(row);
    });

    // Event: Submit form
    $("#btnSimpan").on("click", function (e) {
        if (!SerialHelper.validate()) {
            e.preventDefault();
            return false;
        }
    });
});

/**
 * Scan barcode dan proses barang dengan serial otomatis
 */
function scanBarcode() {
    const kode = ScannerHelper.getValue();

    if (!ScannerHelper.isValid()) {
        return;
    }

    ApiHelper.searchBarang(kode, window.barangKeluarConfig)
        .done((response) => {
            if (!response.status) {
                alert(response.message);
                ScannerHelper.reset();
                return;
            }

            const barang = response.data;

            // Validasi serial tersedia
            if (!Array.isArray(barang.serials) || barang.serials.length === 0) {
                alert("Barang ini tidak memiliki serial yang tersedia.");
                ScannerHelper.reset();
                return;
            }

            // Tambah barang dan serial otomatis
            processBarang(barang);
            ScannerHelper.reset();
        })
        .fail(() => {
            alert("Terjadi kesalahan saat mencari barang.");
            ScannerHelper.reset();
        });
}

/**
 * Proses penambahan barang dan serial
 */
function processBarang(barang) {
    const nomorTransaksi = $('input[name="nomor_transaksi"]').val();
    let $row = TableHelper.getRowByBarangId(barang.id);

    // Jika barang sudah ada, gunakan row yang sudah ada
    if (!$row || !$row.length) {
        $row = TableHelper.createRowKeluar(barang, nomorTransaksi);
    }

    // Tambah semua serial otomatis
    const index = $row.attr("data-barang-index");
    barang.serials.forEach((serial) => {
        SerialHelper.addAutomatic($row, index, serial);
    });
}
