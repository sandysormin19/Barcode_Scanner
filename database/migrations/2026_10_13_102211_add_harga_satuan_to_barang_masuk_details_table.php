<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_masuk_details', function (Blueprint $table) {
            $table->decimal('harga_satuan', 15, 2)->default(0)->after('qty');
        });
    }

    public function down(): void
    {
        Schema::table('barang_masuk_details', function (Blueprint $table) {
            $table->dropColumn('harga_satuan');
        });
    }
};
