$(document).ready(function(){


    $('.btnHapusBarang').click(function(e){

        if(!confirm('Yakin hapus barang ini?')){

            e.preventDefault();

        }


    });


});
