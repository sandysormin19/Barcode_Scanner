<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->string('item_barang')->nullable()->after('kode_barang');
            $table->decimal('harga_satuan', 15, 2)->default(0)->after('item_barang');
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn(['item_barang', 'harga_satuan']);
        });
    }
};
