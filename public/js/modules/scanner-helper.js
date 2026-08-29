
const ScannerHelper = {
    /**
     * Fokus ke input scanner
     */
    focus(selector = "#kode_barang") {
        $(selector).focus();
    },

    /**
     * Reset scanner (kosongkan dan fokus)
     */
    reset(selector = "#kode_barang") {
        $(selector).val("").focus();
    },

    /**
     * Auto-focus setiap N detik
     */
    autoFocus(selector = "#kode_barang", interval = 10000) {
        setInterval(() => {
            this.focus(selector);
        }, interval);
    },

    /**
     * Dapatkan nilai input scanner
     */
    getValue(selector = "#kode_barang") {
        return $(selector).val().trim();
    },

    /**
     * Validasi input scanner tidak kosong
     */
    isValid(selector = "#kode_barang") {
        return this.getValue(selector) !== "";
    },
};
