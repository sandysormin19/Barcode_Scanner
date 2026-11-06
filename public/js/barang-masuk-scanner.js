$(document).ready(function () {
    ScannerHelper.focus();

    $(document).on("keydown", "#kode_barang", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            scanBarcode();
        }
    });

    $(document).on("click", ".btnhapus", function () {
        TableHelper.deleteRow($(this).closest("tr"));
        TableHelper.updateRowNumbers();
    });

    $(document).on("click", ".btnTambahSerial", function () {
        const row = $(this).closest("tr");
        const index = row.attr("data-barang-index");

        const totalSaatIni = row
            .find(".serialQty")
            .toArray()
            .reduce(function (total, el) {
                return total + (parseInt($(el).val(), 10) || 0);
            }, 0);

        const qtyBarang = parseInt(row.find(".qtyMasuk").val(), 10) || 0;

        SerialHelper.addRow(row, index);

        const $newRow = row.find(".serial-row").last();
        if (qtyBarang > totalSaatIni) {
            const sisa = Math.max(1, qtyBarang - totalSaatIni);
            $newRow.find(".serialQty").val(sisa);
        }
    });

    $(document).on("click", ".btnHapusSerial", function () {
        SerialHelper.deleteRow($(this));
    });

    $(document).on("input", ".qtyMasuk", function () {
        const $row = $(this).closest("tr");
        let qty = parseInt($(this).val(), 10) || 0;

        if (qty < 0) qty = 0;

        $(this).val(qty);

        const totalSerial = $row
            .find(".serialQty")
            .toArray()
            .reduce(function (total, input) {
                return total + (parseInt($(input).val(), 10) || 0);
            }, 0);

        if (totalSerial === 0 && qty > 0) {
            const $firstSerial = $row.find(".serial-row").first();
            if ($firstSerial.length) {
                $firstSerial.find(".serialQty").val(qty);
            }
        }

        SerialHelper.calculateTotal($row);
    });

    $(document).on("input change", ".serialQty", function () {
        const $row = $(this).closest("tr");
        let totalSerial = 0;

        $row.find(".serialQty").each(function () {
            totalSerial += parseInt($(this).val(), 10) || 0;
        });

        $row.find(".qtyMasuk").val(totalSerial);
        SerialHelper.calculateTotal($row);
    });

    $(document).on("input", ".serialNumber", function () {
        $(this).closest(".serial-row").attr("data-serial-number", $(this).val().trim());
    });

    $(document).on("input change", ".scanSerialQty", function () {
        const $group = $(this).closest(".scan-serial-group");
        $group.find(".scan-batch-qty").val($(this).val());
    });

    $("#btnSimpan").on("click", function (event) {
        if (!SerialHelper.validate()) {
            event.preventDefault();
            return false;
        }
    });
});

function scanBarcode() {
    const barcodeValue = ScannerHelper.getValue();

    if (!ScannerHelper.isValid()) {
        return;
    }

    ApiHelper.searchBarang(barcodeValue, window.barangMasukConfig)
        .done(function (response) {
            if (!response.status) {
                alert(response.message || "Barang tidak ditemukan.");
                ScannerHelper.reset();
                return;
            }

            const barang = response.data;

            try {
                addToCart(barang);
            } catch (error) {
                const message =
                    error.message || "Serial number gagal dimasukkan.";

                $("#scannerStatus")
                    .removeClass("text-success")
                    .addClass("text-danger")
                    .text(message);

                alert(message);
                ScannerHelper.reset();
                return;
            }

            $("#scannerStatus")
                .removeClass("text-danger")
                .addClass("text-success")
                .text(
                    "Terbaca: " +
                    barang.item_barang +
                    " | Serial: " +
                    barang.serial_number +
                    " | Batch: " +
                    (barang.batch || "otomatis saat disimpan")
                );

            ScannerHelper.reset();
        })
        .fail(function (xhr) {
            const message =
                xhr.responseJSON?.message || "Gagal membaca barcode.";

            $("#scannerStatus")
                .removeClass("text-success")
                .addClass("text-danger")
                .text(message);

            alert(message);
            ScannerHelper.reset();
        });
}

function addToCart(barang) {
    let $row = TableHelper.getRowByBarangId(barang.id);

    if (!barang.pakai_no_seri) {
        if (!$row || !$row.length) {
            const nomorTransaksi = $('input[name="nomor_transaksi"]').val();
            TableHelper.createRowMasuk(barang, nomorTransaksi);
            return;
        }

        const $qtyInput = $row.find('.qtyMasuk');
        $qtyInput.val((parseInt($qtyInput.val(), 10) || 0) + 1);
        return;
    }
    $row = TableHelper.getRowByBarangId(barang.id);
    const isNewRow = !$row || !$row.length;

    if (isNewRow) {
        const nomorTransaksi = $('input[name="nomor_transaksi"]').val();
        $row = TableHelper.createRowMasuk(barang, nomorTransaksi);

        $row.find(".serial-container").empty();
        $row.find(".btnTambahSerial").show();
    }

    const serial = String(barang.serial_number || "").trim();
    const batch = String(barang.batch || "").trim();

    if (serial === "") {
        throw new Error("Barcode harus berisi nomor seri.");
    }

    const index = $row.attr("data-barang-index");

    let $group = $row
        .find(".scan-serial-group")
        .filter(function () {
            return $(this).attr("data-serial-number") === serial
                && $(this).attr("data-batch") === batch;
        })
        .first();
    const isNewGroup = !$group.length;

    if (isNewGroup) {
        $group = $("<div>", {
            class: "serial-row scan-serial-group mb-2",
        }).attr("data-serial-number", serial).attr("data-batch", batch);

        const $inputGroup = $("<div>", {
            class: "input-group",
        });

        const $serialInput = $("<input>", {
            type: "text",
            class: "form-control serialNumber",
            value: serial,
            placeholder: "Nomor Seri",
        });

        const $qtyInput = $("<input>", {
            type: "number",
            class: "form-control serialQty scanSerialQty",
            value: 1,
            min: 1,
            placeholder: "Qty",
        });

        const $deleteButton = $("<button>", {
            type: "button",
            class: "btn btn-danger btnHapusSerial",
            text: "Hapus",
        });

        const $deleteWrapper = $("<div>", {
            class: "input-group-append",
        }).append($deleteButton);

        const $batchList = $("<small>", {
            class: "d-block text-muted scan-batch-list",
        });

        const $hiddenInputs = $("<div>", {
            class: "scan-batch-inputs",
        });

        $inputGroup
            .append($serialInput)
            .append($qtyInput)
            .append($deleteWrapper);

        $group.append($inputGroup).append($batchList).append($hiddenInputs);

        $row.find(".serial-container").append($group);
    }

    const $scanQty = $group.find(".scanSerialQty");
    const qtySaatIni = parseInt($scanQty.val(), 10) || 0;
    if (!isNewGroup) {
        $scanQty.val(qtySaatIni + 1);
    }

    const $hiddenInputs = $group.find(".scan-batch-inputs");
    let $batchQty = $hiddenInputs.find(".scan-batch-qty");

    if (!$batchQty.length) {
        $("<input>", {
            type: "hidden",
            name: "serial_number[" + index + "][]",
            value: serial,
        }).appendTo($hiddenInputs);

        $("<input>", {
            type: "hidden",
            name: "serial_batch[" + index + "][]",
            value: batch,
        }).appendTo($hiddenInputs);

        $batchQty = $("<input>", {
            type: "hidden",
            class: "scan-batch-qty",
            name: "serial_qty[" + index + "][]",
            value: $scanQty.val(),
        }).appendTo($hiddenInputs);
    } else {
        $batchQty.val($scanQty.val());
    }

    const batchAlreadyShown = $group
        .find(".scan-batch-list span")
        .toArray()
        .some((element) => String($(element).text()).trim() === batch);

    if (!batchAlreadyShown) {
        $("<span>", {
            class: "d-inline-block mr-2",
            text: batch || "Batch otomatis saat disimpan",
        }).appendTo($group.find(".scan-batch-list"));
    }

    const totalQty = $row
        .find(".scanSerialQty, .serialQty")
        .toArray()
        .reduce(function (total, input) {
            return total + (parseInt($(input).val(), 10) || 0);
        }, 0);

    $row.find(".qtyMasuk").val(totalQty);
    SerialHelper.calculateTotal($row);
}
