

$(document).ready(function () {
    // Event: Tombol cari
    $(document).on("click", "#btnCari", function () {
        searchBarang();
    });

    // Event: Perubahan qty
    $(document).on("change", ".qtyKeluar", function () {
        validateQty($(this));
    });

    // Event: Hapus barang
    $(document).on("click", ".btnHapus", function () {
        $(this).closest("tr").remove();
    });
});

/**
 * Cari barang
 */
function searchBarang() {
    const kode = ScannerHelper.getValue("#kode_barang");

    if (!kode) {
        showAlert("Kode barang harus diisi");
        return;
    }

    ApiHelper.searchBarang(kode, window.barangKeluarConfig)
        .done((response) => {
            if (!response.status) {
                showAlert(response.message);
                ScannerHelper.reset("#kode_barang");
                return;
            }

            const barang = response.data;

            // Validasi stok
            if (parseInt(barang.stok) <= 0) {
                showAlert(
                    "Stok barang habis. Silakan lakukan Barang Masuk dahulu.",
                );
                ScannerHelper.reset("#kode_barang");
                return;
            }

            addToCart(barang);
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
        showAlert("Barang sudah ada di daftar");
        ScannerHelper.reset("#kode_barang");
        return;
    }

    TableHelper.createRowSimple(barang);
}

/**
 * Validasi qty tidak melebihi stok
 */
function validateQty($qtyInput) {
    const qty = parseInt($qtyInput.val());
    const stok = parseInt($qtyInput.attr("max"));

    if (qty > stok) {
        showAlert("Qty melebihi stok tersedia");
        $qtyInput.val(stok);
    }
}
