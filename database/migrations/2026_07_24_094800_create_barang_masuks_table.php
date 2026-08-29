<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
      Schema::create('barang_masuks', function (Blueprint $table) {

    $table->id();

    $table->string('nomor_transaksi')->unique();

    $table->date('tanggal_masuk');

    $table->text('keterangan')->nullable();

    $table->timestamps();

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_masuks');
    }
};
