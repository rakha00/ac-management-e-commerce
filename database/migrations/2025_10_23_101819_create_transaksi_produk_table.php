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
        Schema::create('transaksi_produk', function (Blueprint $table) {
            $table->id();

            $table->date('tanggal_transaksi');
            $table->string('nomor_invoice');
            $table->string('nomor_surat_jalan');

            $table->foreignId('sales_karyawan_id')->nullable()->constrained('karyawan')->onDelete('set null');
            $table->string('sales_nama')->nullable();
            $table->string('toko_konsumen');

            $table->text('keterangan')->nullable();

            $table->unique(['tanggal_transaksi', 'nomor_invoice'], 'unique_invoice_per_tanggal');
            $table->unique(['tanggal_transaksi', 'nomor_surat_jalan'], 'unique_surat_jalan_per_tanggal');

            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_produk');
    }
};
