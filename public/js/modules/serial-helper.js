const SerialHelper = {

    addAutomatic(row, index, serial) {
        const serialNumber = String(serial.serial_number || '').trim();
        const batch = String(serial.batch || '').trim();
        const available = parseInt(serial.qty_tersedia, 10) || 0;

        if (!serialNumber || !batch || available < 1) {
            return;
        }

        const escapedSerial = $('<div>').text(serialNumber).html();
        const escapedBatch = $('<div>').text(batch).html();
        const $existing = row.find('.serial-row').filter(function () {
            const existingSerial = $(this).find('input[name^="serial_number["]').val();
            const existingBatch = $(this).find('input[name^="serial_batch["]').val();
            return String(existingSerial || '').trim() === serialNumber
                && String(existingBatch || '').trim() === batch;
        }).first();

        if ($existing.length) {
            const $qty = $existing.find('.serialQty');
            const current = parseInt($qty.val(), 10) || 0;
            if (current >= available) {
                return;
            }
            $qty.val(current + 1).trigger('input');
            $existing.find('small.text-muted').text(
                `Batch: ${batch} (tersedia ${Math.max(0, available - current - 1)})`
            );
            return;
        }

        const html = `
            <div class="serial-row mb-2">
                <div class="input-group">
                    <input type="text" class="form-control serialNumber"
                           name="serial_number[${index}][]" value="${escapedSerial}" readonly>
                    <input type="number" class="form-control serialQty"
                           name="serial_qty[${index}][]" value="1" min="1" max="${available}">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btnHapusSerial">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <small class="text-muted">Batch: ${escapedBatch} (stok serial ${available}, tersedia setelah dipilih ${Math.max(0, available - 1)})</small>
                <input type="hidden" name="serial_batch[${index}][]" value="${escapedBatch}">
            </div>
        `;

        row.find('.serial-container').append(html);
        this.calculateTotal(row);
    },

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

    deleteRow($serialRow) {
        const $row = $serialRow.closest("tr");
        $serialRow.closest(".serial-row").remove();
        this.calculateTotal($row);

        const totalSerial = $row
            .find(".serialQty")
            .toArray()
            .reduce(function (sum, input) {
                return sum + (parseInt($(input).val(), 10) || 0);
            }, 0);

        $row.find(".qtyMasuk").val(totalSerial);
    },

    calculateTotal(row) {
        let totalSerial = 0;

        row.find(".serialQty").each(function () {
            totalSerial += parseInt($(this).val()) || 0;
        });

        const qtyBarang =
            parseInt(row.find(".qtyMasuk, .qtyKeluar").val()) || 0;

        row.find(".totalSerial").text(totalSerial);
        row.find(".totalBarang").text(qtyBarang);

        const $totalSerial = row.find(".totalSerial");
        if (totalSerial === qtyBarang) {
            $totalSerial.removeClass("text-danger").addClass("text-success");
        } else {
            $totalSerial.removeClass("text-success").addClass("text-danger");
        }

        if (row.find(".qtyKeluar").length) {
            row.find(".qtyKeluar").val(totalSerial);
        }
    },

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

            if (row.find(".serialQty").length > 0 && totalSerial !== qtyBarang) {
                isValid = false;
                errors.push("Total Qty Serial harus sama dengan Qty Barang");
                row.find(".totalSerial").addClass("text-danger");
                return;
            }

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
    }
};
