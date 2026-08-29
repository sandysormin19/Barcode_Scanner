<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kategori_id')
                ->nullable()
                ->constrained('kategoris')
                ->nullOnDelete();

            $table->string('kode_barang', 50)->unique();
            $table->string('nama_barang');
            $table->string('unit', 20);

            $table->string('barang_induk')->nullable();

            $table->string('brand')->nullable();
            $table->string('item_sablon')->nullable();
            $table->string('proses')->nullable();
            $table->string('handfeel')->nullable();

            $table->boolean('pakai_no_seri')->default(false);
            $table->boolean('paksa_no_seri')->default(false);

            $table->string('tipe_seri_batch')->nullable();
            $table->string('tipe_barang')->nullable();

            $table->string('departemen')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
