

$(function () {
    // Event: Tombol cari atau Enter
    $(document).on("click", "#btnCari", function () {
        scanBarcode();
    });

    $(document).on("keypress", "#kode_barang", function (e) {
        if (e.which === 13) {
            e.preventDefault();
            scanBarcode();
        }
    });

    // Event: Hapus barang
    $(document).on("click", ".btnHapus", function () {
        $(this).closest("tr").remove();
        updateNomor();

        if ($("#detailBarang tr").length == 0) {
            showEmptyTable();
        }
    });
});

/**
 * Scan / Cari barcode
 */
function scanBarcode() {
    const kode = ScannerHelper.getValue("#kode_barang");

    if (!kode) {
        showAlert("Kode barang harus diisi");
        return;
    }

    ApiHelper.searchBarang(kode, window.barangMasukConfig)
        .done((response) => {
            if (!response.status) {
                showAlert(response.message);
                ScannerHelper.reset("#kode_barang");
                return;
            }

            addToCart(response.data);
            ScannerHelper.reset("#kode_barang");
        })
        .fail(() => {
            showAlert("Terjadi kesalahan saat mencari barang.");
            ScannerHelper.reset("#kode_barang");
        });
}

/**
 * Tambah barang ke keranjang
 */
function addToCart(barang) {
    if (TableHelper.barangExists(barang.id)) {
        const $row = TableHelper.getRowByBarangId(barang.id);
        const $qtyInput = $row.find(".qtyMasuk");
        $qtyInput.val(parseInt($qtyInput.val()) + 1);
        return;
    }

    TableHelper.createRowSimple(barang);
}

/**
 * Update nomor urut baris
 */
function updateNomor() {
    $("#detailBarang tr").each((i) => {
        $(this)
            .find("td:first")
            .text(i + 1);
    });
}

/**
 * Tampilkan tabel kosong
 */
function showEmptyTable() {
    TableHelper.showEmptyMessage("Belum ada barang yang dipilih.");
}
