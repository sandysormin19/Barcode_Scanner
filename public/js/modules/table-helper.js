/**
 * Table Helper - Mengelola operasi tabel (create, update, delete rows)
 */
const TableHelper = {
    barangIndex: 0,

    /**
     * Buat baris barang baru untuk masuk
     */
    createRowMasuk(
        barang,
        nomorTransaksi,
        containerSelector = "#detailBarang",
    ) {
        this.clearEmptyMessage(containerSelector);

        const index = this.barangIndex++;
        const nomor = $(containerSelector + " tr").length + 1;

        const html = `
            <tr data-barang-index="${index}">
                <td class="nomor-baris">${nomor}</td>
                <td>
                    ${barang.kode_barang}
                    <input type="hidden" name="barang_id[]" value="${barang.id}">
                </td>
                <td>${barang.nama_barang}</td>
                <td>${barang.unit ?? "-"}</td>
                <td>
                    <input type="number" class="form-control qtyMasuk"
                           name="qty[]" value="1" min="1">
                </td>
                <td>
                    <input type="text" class="form-control"
                           value="${nomorTransaksi}" readonly>
                </td>
                <td>
                    <div class="serial-container">
                        <div class="serial-row mb-2">
                            <div class="input-group">
                                <input type="text" class="form-control serialNumber"
                                       name="serial_number[${index}][]"
                                       placeholder="Nomor Seri">
                                <input type="number" class="form-control serialQty"
                                       name="serial_qty[${index}][]" value="1" min="1"
                                       placeholder="Qty">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger btnHapusSerial">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary btnTambahSerial">
                        <i class="fas fa-plus mr-1"></i> Tambah Serial
                    </button>
                    <div class="mt-2">
                        <small>Total Serial: <strong class="totalSerial text-danger">1</strong> /
                               <strong class="totalBarang">1</strong></small>
                    </div>
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

    /**
     * Buat baris barang baru untuk keluar
     */
    createRowKeluar(
        barang,
        nomorTransaksi,
        containerSelector = "#detailBarang",
    ) {
        this.clearEmptyMessage(containerSelector);

        const index = this.barangIndex++;
        const nomor = $(containerSelector + " tr").length + 1;

        const html = `
            <tr data-barang-index="${index}">
                <td class="nomor-baris">${nomor}</td>
                <td>
                    ${barang.kode_barang}
                    <input type="hidden" name="barang_id[]" value="${barang.id}">
                </td>
                <td>${barang.nama_barang}</td>
                <td>${barang.stok}</td>
                <td>
                    <input type="number" class="form-control qtyKeluar"
                           name="qty[]" value="0" min="1"
                           max="${barang.stok}" readonly>
                </td>
                <td>
                    <input type="text" class="form-control"
                           value="${nomorTransaksi}" readonly>
                </td>
                <td>
                    <div class="serial-container"></div>
                    <div class="mt-2">
                        <small>Total Serial: <strong class="totalSerial text-danger">0</strong> /
                               <strong class="totalBarang">0</strong></small>
                    </div>
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

    /**
     * Buat baris barang standar (tanpa serial)
     */
    createRowSimple(barang, containerSelector = "#detailBarang") {
        this.clearEmptyMessage(containerSelector);

        const nomor = $(containerSelector + " tr").length + 1;

        const html = `
            <tr>
                <td>${nomor}</td>
                <td>
                    ${barang.kode_barang}
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

    /**
     * Hapus baris barang
     */
    deleteRow($row) {
        $row.remove();
    },

    /**
     * Perbarui nomor urut baris
     */
    updateRowNumbers(containerSelector = "#detailBarang") {
        $(containerSelector + " tr").each((index, element) => {
            $(element)
                .find(".nomor-baris")
                .text(index + 1);
        });
    },

    /**
     * Hapus pesan kosong di tabel
     */
    clearEmptyMessage(containerSelector = "#detailBarang") {
        if ($(containerSelector + " tr td").length === 1) {
            $(containerSelector).html("");
        }
    },

    /**
     * Tampilkan pesan tabel kosong
     */
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

    /**
     * Cek apakah barang sudah ada
     */
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

    /**
     * Dapatkan row berdasarkan ID barang
     */
    getRowByBarangId(barangId) {
        let result = null;
        $('input[name="barang_id[]"]').each(function () {
            if ($(this).val() == barangId) {
                result = $(this).closest("tr");
                return false;
            }
        });
        return result;
    },
};
