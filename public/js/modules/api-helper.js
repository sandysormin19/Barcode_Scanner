
const ApiHelper = {
    /**
     * Cari barang berdasarkan kode
     */
    searchBarang(kode, config) {
        return $.ajax({
            url: config.cariUrl,
            type: "GET",
            data: { kode_barang: kode },
            dataType: "json",
        });
    },
};
