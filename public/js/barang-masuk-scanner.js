

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

    // Event: Tambah serial
    $(document).on("click", ".btnTambahSerial", function () {
        const row = $(this).closest("tr");
        const index = row.attr("data-barang-index");
        SerialHelper.addRow(row, index);
    });

    // Event: Hapus serial
    $(document).on("click", ".btnHapusSerial", function () {
        SerialHelper.deleteRow($(this));
    });

    // Event: Input qty barang
    $(document).on("input", ".qtyMasuk", function () {
        const row = $(this).closest("tr");
        SerialHelper.calculateTotal(row);
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
 * Scan dan proses barang
 */
function scanBarcode() {
    const kode = ScannerHelper.getValue();

    if (!ScannerHelper.isValid()) {
        return;
    }

    ApiHelper.searchBarang(kode, window.barangMasukConfig)
        .done((response) => {
            if (!response.status) {
                alert(response.message);
                ScannerHelper.reset();
                return;
            }

            addToCart(response.data);
            ScannerHelper.reset();
        })
        .fail(() => {
            alert("Terjadi kesalahan saat mencari barang.");
            ScannerHelper.reset();
        });
}

/**
 * Tambah barang ke keranjang (update atau create)
 */
function addToCart(barang) {
    const $existingRow = TableHelper.getRowByBarangId(barang.id);

    if ($existingRow && $existingRow.length) {
        // Update qty jika barang sudah ada
        const $qtyInput = $existingRow.find(".qtyMasuk");
        const currentQty = parseInt($qtyInput.val()) || 0;
        $qtyInput.val(currentQty + 1);
        SerialHelper.calculateTotal($existingRow);
        return;
    }

    // Buat baris baru
    const nomorTransaksi = $('input[name="nomor_transaksi"]').val();
    const $newRow = TableHelper.createRowMasuk(barang, nomorTransaksi);
    SerialHelper.calculateTotal($newRow);
}
