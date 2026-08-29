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
        Schema::create('barang_keluar_details', function (Blueprint $table) {
             $table->id();

    $table->foreignId('barang_keluar_id')
        ->constrained('barang_keluars')
        ->cascadeOnDelete();

    $table->foreignId('barang_id')
        ->constrained('barangs')
        ->cascadeOnDelete();

    $table->integer('qty');

    $table->string('batch')->nullable();

    $table->string('serial_number')->nullable();

    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_keluar_details');
    }
};
