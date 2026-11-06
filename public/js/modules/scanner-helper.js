
const ScannerHelper = {
    focus(selector = "#kode_barang") {
        $(selector).focus();
    },


    reset(selector = "#kode_barang") {
        $(selector).val("").focus();
    },


    getValue(selector = "#kode_barang") {
        return $(selector).val().trim();
    },


    isValid(selector = "#kode_barang") {
        return this.getValue(selector) !== "";
    },
};
