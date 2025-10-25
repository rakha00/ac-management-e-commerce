<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi_produks', function (Blueprint $table) {
            $table->id();

            // Core transaction identity
            $table->date('tanggal_transaksi'); // transaction date
            $table->string('nomor_invoice'); // auto-generated per date, unique & sequential
            $table->string('nomor_surat_jalan'); // auto-generated per date, unique & sequential

            // Sales info (sourced from karyawan.jabatan = 'sales')
            $table->foreignId('sales_karyawan_id')
                ->nullable()
                ->constrained('karyawan')
                ->nullOnDelete();
            $table->string('sales_nama')->nullable();

            // Store / consumer (plain string per requirement)
            $table->string('toko_konsumen');

            // Transaction totals
            $table->decimal('total_modal', 15, 2)->default(0);
            $table->decimal('total_penjualan', 15, 2)->default(0);
            $table->decimal('total_keuntungan', 15, 2)->default(0);

            // Notes
            $table->text('keterangan')->nullable();

            // Ensure numbers are unique per date
            $table->unique(['tanggal_transaksi', 'nomor_invoice'], 'unique_invoice_per_tanggal');
            $table->unique(['tanggal_transaksi', 'nomor_surat_jalan'], 'unique_surat_jalan_per_tanggal');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_produks');
    }
};
