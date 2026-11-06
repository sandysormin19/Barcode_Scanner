<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_masuk_serials', function (Blueprint $table) {
            $table->string('batch')->nullable()->after('serial_number');
        });

        Schema::table('barang_keluar_serials', function (Blueprint $table) {
            $table->string('batch')->nullable()->after('serial_number');
        });
    }

    public function down(): void
    {
        Schema::table('barang_keluar_serials', function (Blueprint $table) {
            $table->dropColumn('batch');
        });

        Schema::table('barang_masuk_serials', function (Blueprint $table) {
            $table->dropColumn('batch');
        });
    }
};
