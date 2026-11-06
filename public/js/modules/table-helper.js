
const TableHelper = {
    barangIndex: 0,

    createRowMasuk(
        barang,
        nomorTransaksi,
        containerSelector = "#detailBarang",
    ) {
        this.clearEmptyMessage(containerSelector);

        const index = this.barangIndex++;
        const nomor = $(containerSelector + " tr[data-barang-index]").length + 1;
        const serialFields = barang.pakai_no_seri
            ? `
                <div class="serial-container">
                    <div class="serial-row mb-2">
                        <div class="input-group">
                            <input type="text" class="form-control serialNumber"
                                   name="serial_number[${index}][]" placeholder="Nomor Seri">
                            <input type="number" class="form-control serialQty"
                                   name="serial_qty[${index}][]" value="1" min="1" placeholder="Qty">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger btnHapusSerial">Hapus</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-primary btnTambahSerial">Tambah Serial</button>
                <div class="mt-2"><small>Total Serial: <strong class="totalSerial text-danger">1</strong> / <strong class="totalBarang">1</strong></small></div>
            `
            : '<span class="text-muted">Tidak menggunakan serial</span>';

        const html = `
            <tr data-barang-index="${index}">
               <td class="nomor-baris">
                 ${nomor}
                <input type="hidden" name="barang_id[${index}]" value="${barang.id}">
                </td>
                <td>${barang.nama_barang}</td>
                <td>${barang.unit ?? "-"}</td>
                <td>
                    <input type="number" class="form-control qtyMasuk"
                           name="qty[${index}]" value="1" min="1">
                </td>
                <td>
                    <input type="text" class="form-control"
                           value="${nomorTransaksi}" readonly>
                </td>
                <td>
                    ${serialFields}
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm btnHapus">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </td>
            </tr>
        `;

        $(containerSelector).append(html);
        return $(`tr[data-barang-index="${index}"]`);
    },

    createRowKeluar(
        barang,
        nomorTransaksi,
        containerSelector = "#detailBarang",
    ) {
        this.clearEmptyMessage(containerSelector);

        const index = this.barangIndex++;
        const nomor = $(containerSelector + " tr[data-barang-index]").length + 1;
        const serialFields = barang.pakai_no_seri
            ? `
                <div class="serial-container"></div>
                <div class="mt-2"><small>Total Serial: <strong class="totalSerial text-danger">0</strong> / <strong class="totalBarang">0</strong></small></div>
            `
            : '<span class="text-muted">Tidak menggunakan serial</span>';

        const html = `
            <tr data-barang-index="${index}">
               <td class="nomor-baris">
                ${nomor}
                <input type="hidden" name="barang_id[${index}]" value="${barang.id}">
                </td>
                <td>${barang.nama_barang}</td>
                <td>${barang.stok}</td>
                <td>
                    <input type="number" class="form-control qtyKeluar"
                           name="qty[${index}]" value="${barang.pakai_no_seri ? 0 : 1}" min="1"
                           max="${barang.stok}" ${barang.pakai_no_seri ? 'readonly' : ''}>
                </td>
                <td>
                    <input type="text" class="form-control"
                           value="${nomorTransaksi}" readonly>
                </td>
                <td>
                    ${serialFields}
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm btnHapus">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </td>
            </tr>
        `;

        $(containerSelector).append(html);
        return $(`tr[data-barang-index="${index}"]`);
    },


    createRowSimple(barang, containerSelector = "#detailBarang") {
        this.clearEmptyMessage(containerSelector);

        const nomor = $(containerSelector + " tr[data-barang-index]").length + 1;

        const html = `
            <tr>
                <td>${nomor}</td>
                <td>
                    ${barang.kode_tampilan ?? String(barang.kode_barang ?? "").replace(/-[a-f0-9]{16}$/i, "")}
                    <input type="hidden" name="barang_id[]" value="${barang.id}">
                </td>
                <td>${barang.nama_barang}</td>
                <td>${barang.stok}</td>
                <td>
                    <input type="number" name="qty[]" class="form-control qtyKeluar"
                           value="1" min="1" max="${barang.stok}">
                </td>
                <td>
                    <input type="text" name="batch[]" class="form-control">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm btnHapus">
                        Hapus
                    </button>
                </td>
            </tr>
        `;

        $(containerSelector).append(html);
    },


    deleteRow($row) {
        $row.remove();
    },


    updateRowNumbers(containerSelector = "#detailBarang") {
        $(containerSelector + " tr").each((index, element) => {
            $(element)
                .find(".nomor-baris")
                .text(index + 1);
        });
    },


    clearEmptyMessage(containerSelector = "#detailBarang") {
        if ($(containerSelector + " tr td").length === 1) {
            $(containerSelector).html("");
        }
    },


    showEmptyMessage(
        message = "Belum ada barang yang dipilih.",
        containerSelector = "#detailBarang",
    ) {
        const html = `
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="fas fa-box-open fa-2x mb-2"></i><br>${message}
                </td>
            </tr>
        `;
        $(containerSelector).html(html);
    },


    barangExists(barangId) {
        let exists = false;
        $('input[name="barang_id[]"]').each(function () {
            if ($(this).val() == barangId) {
                exists = true;
                return false;
            }
        });
        return exists;
    },


getRowByBarangId(barangId) {
    let $result = $();

    $('#detailBarang input[name^="barang_id["]').each(function () {
        if (String($(this).val()) === String(barangId)) {
            $result = $(this).closest("tr");
            return false;
        }
    });

    return $result;
},
};
