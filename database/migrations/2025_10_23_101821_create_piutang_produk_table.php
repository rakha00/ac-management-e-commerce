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
        Schema::create('piutang_produk', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('transaksi_barang_masuk_id')->constrained('transaksi_barang_masuk')->onDelete('cascade');
            $table->string('no_invoice'); // nomor invoice dari BarangMasuk
            $table->integer('total_piutang'); // total piutang dari BarangMasuk
            $table->integer('sisa_piutang');
            $table->string('status_pembayaran');
            $table->date('jatuh_tempo');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piutang_produk');
    }
};
