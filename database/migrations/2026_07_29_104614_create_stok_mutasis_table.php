<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stok_mutasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')
                  ->constrained('barangs')
                  ->cascadeOnDelete();
            $table->string('nomor_transaksi');
            $table->enum('jenis', [
                'MASUK',
                'KELUAR',
                'OPNAME'
            ]);

            $table->integer('qty');

            $table->string('batch')->nullable();

            $table->string('serial_number')->nullable();

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_mutasis');
    }
};
