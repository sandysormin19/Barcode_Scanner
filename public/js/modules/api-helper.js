const ApiHelper = {
    searchBarang(barcodeValue, config) {
        return $.ajax({
            url: config.cariUrl,
            type: "GET",
            data: {
                kode_barang: barcodeValue,
            },
            dataType: "json",
        });
    },
};
