

$(document).ready(function () {

    ScannerHelper.focus();

    $(document).on("keydown", "#kode_barang", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            scanBarcode();
        }
    });


    $(document).on("click", ".btnHapus", function () {
        TableHelper.deleteRow($(this).closest("tr"));
        TableHelper.updateRowNumbers();
    });

    $(document).on("click", ".btnHapusSerial", function () {
        SerialHelper.deleteRow($(this));
    });

    $(document).on("input", ".serialQty", function () {
        const row = $(this).closest("tr");
        SerialHelper.calculateTotal(row);
    });

    $("#btnSimpan").on("click", function (e) {
        if (!SerialHelper.validate()) {
            e.preventDefault();
            return false;
        }
    });
});


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
                const stokTersedia = Number(barang.stok ?? 0);

            if (stokTersedia <= 0) {
              alert("Stok barang tidak mencukupi.");
                 ScannerHelper.reset();
                 return;
                }



            if (barang.pakai_no_seri && (!Array.isArray(barang.serials) || barang.serials.length === 0)) {
                alert("Tidak ada serial tersedia untuk barang ini.");
                ScannerHelper.reset();
                return;
            }

            // Tambah barang dan serial otomatis
            processBarang(barang);
            ScannerHelper.reset();
        })
        .fail((xhr) => {
            const message = xhr.responseJSON?.message
                || "Gagal mencari barang. Periksa koneksi server dan database.";
            alert(message);
            ScannerHelper.reset();
        });
}


function processBarang(barang) {
    const nomorTransaksi = $('input[name="nomor_transaksi"]').val();
    let $row = TableHelper.getRowByBarangId(barang.id);

    if (!barang.pakai_no_seri) {
        if (!$row || !$row.length) {
            TableHelper.createRowKeluar(barang, nomorTransaksi);
            return;
        }

        const $qtyInput = $row.find(".qtyKeluar");
        const nextQty = (parseInt($qtyInput.val(), 10) || 0) + 1;
        if (nextQty > Number(barang.stok)) {
            alert("Qty melebihi stok tersedia.");
            return;
        }
        $qtyInput.val(nextQty);
        return;
    }

    if (!$row || !$row.length) {
        $row = TableHelper.createRowKeluar(barang, nomorTransaksi);
    }

    const index = $row.attr("data-barang-index");
    barang.serials.forEach((serial) => {
        SerialHelper.addAutomatic($row, index, serial);
    });
}
