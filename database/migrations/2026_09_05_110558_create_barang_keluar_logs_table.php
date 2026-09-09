<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('barang_keluar_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_keluar_id')
                ->constrained('barang_keluars')
                ->cascadeOnDelete();
            $table->string('status_from')->nullable();
            $table->string('status_to');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamp('tanggal_perubahan')->useCurrent();
            $table->timestamps();

            $table->index(['barang_keluar_id', 'tanggal_perubahan']);

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('barang_keluar_logs');
    }
};
