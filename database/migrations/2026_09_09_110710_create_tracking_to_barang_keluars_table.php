<?php

use App\Enums\StatusPengiriman;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->string('status')->default(StatusPengiriman::Pending->value)->after('nomor_transaksi');
            $table->timestamp('status_updated_at')->nullable()->after('status');
            $table->string('courier')->nullable()->after('status_updated_at');
            $table->string('destination')->nullable()->after('courier');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'status_updated_at', 'courier', 'destination']);
        });
    }
};
