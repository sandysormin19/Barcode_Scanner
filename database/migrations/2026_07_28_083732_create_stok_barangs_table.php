<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_barangs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('barang_id')
                  ->constrained('barangs')
                  ->cascadeOnDelete();

            $table->integer('stok')->default(0);

            $table->timestamps();

            $table->unique('barang_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_barangs');
    }
};
