/**
 * Serial Helper - Mengelola operasi serial (create, delete, calculate)
 */
const SerialHelper = {
    /**
     * Tambah baris serial baru
     */
    addRow(row, index) {
        const html = `
            <div class="serial-row mb-2">
                <div class="input-group">
                    <input type="text" class="form-control serialNumber"
                           name="serial_number[${index}][]" placeholder="Nomor Seri">
                    <input type="number" class="form-control serialQty"
                           name="serial_qty[${index}][]" value="1" min="1" placeholder="Qty">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btnHapusSerial">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        row.find(".serial-container").append(html);
        this.calculateTotal(row);
    },

    /**
     * Tambah serial otomatis dengan nilai default
     */
    addAutomatic(row, index, serial) {
        // Cek apakah serial sudah ada
        if (this.serialExists(row, serial.serial_number)) {
            return false;
        }

        const html = `
            <div class="serial-row mb-2">
                <div class="input-group">
                    <input type="text" class="form-control serialNumber"
                           name="serial_number[${index}][]"
                           value="${serial.serial_number}" readonly>
                    <input type="number" class="form-control serialQty"
                           name="serial_qty[${index}][]" value="${serial.qty_tersedia}"
                           min="1" max="${serial.qty_tersedia}">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btnHapusSerial">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <small class="text-muted">Tersedia: ${serial.qty_tersedia}</small>
            </div>
        `;
        row.find(".serial-container").append(html);
        this.calculateTotal(row);
        return true;
    },

    /**
     * Hapus baris serial
     */
    deleteRow($serialRow) {
        const $row = $serialRow.closest("tr");
        $serialRow.closest(".serial-row").remove();
        this.calculateTotal($row);
    },

    /**
     * Hitung total serial dan sinkronkan dengan qty barang
     */
    calculateTotal(row) {
        let totalSerial = 0;

        row.find(".serialQty").each(function () {
            totalSerial += parseInt($(this).val()) || 0;
        });

        const qtyBarang =
            parseInt(row.find(".qtyMasuk, .qtyKeluar").val()) || 0;

        row.find(".totalSerial").text(totalSerial);
        row.find(".totalBarang").text(qtyBarang);

        // Update status warna
        const $totalSerial = row.find(".totalSerial");
        if (totalSerial === qtyBarang) {
            $totalSerial.removeClass("text-danger").addClass("text-success");
        } else {
            $totalSerial.removeClass("text-success").addClass("text-danger");
        }

        // Untuk barang keluar, update qty barang otomatis
        if (row.find(".qtyKeluar").length) {
            row.find(".qtyKeluar").val(totalSerial);
        }
    },

    /**
     * Validasi serial sebelum submit
     */
    validate() {
        let isValid = true;
        const errors = [];

        $("#detailBarang tr[data-barang-index]").each(function () {
            const row = $(this);
            const qtyBarang =
                parseInt(row.find(".qtyMasuk, .qtyKeluar").val()) || 0;
            let totalSerial = 0;

            row.find(".serialQty").each(function () {
                totalSerial += parseInt($(this).val()) || 0;
            });

            // Cek kecocokan total
            if (
                row.find(".serialQty").length > 0 &&
                totalSerial !== qtyBarang
            ) {
                isValid = false;
                errors.push("Total Qty Serial harus sama dengan Qty Barang");
                row.find(".totalSerial").addClass("text-danger");
                return;
            }

            // Cek serial kosong
            let hasEmpty = false;
            row.find(".serialNumber").each(function () {
                if ($(this).val().trim() === "") {
                    hasEmpty = true;
                    return false;
                }
            });

            if (hasEmpty) {
                isValid = false;
                errors.push("Nomor Serial tidak boleh kosong");
                return false;
            }
        });

        if (!isValid && errors.length > 0) {
            alert(errors[0]);
        }

        return isValid;
    },

    /**
     * Cek apakah serial sudah ada di row
     */
    serialExists(row, serialNumber) {
        let exists = false;
        row.find(".serial-row").each(function () {
            const current = $(this).find(".serialNumber").val();
            if (String(current) === String(serialNumber)) {
                exists = true;
                return false;
            }
        });
        return exists;
    },
};
