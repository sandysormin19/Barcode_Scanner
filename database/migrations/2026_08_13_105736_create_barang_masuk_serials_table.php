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
        Schema::create('barang_masuk_serials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('barang_masuk_detail_id')
                  ->constrained('barang_masuk_details')
                  ->cascadeOnDelete();

            $table->string('serial_number');
            $table->integer('qty');
            $table->index("serial_number");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_masuk_serials');
    }
};
