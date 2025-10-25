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
        Schema::create('transaksi_jasas', function (Blueprint $table) {
            $table->id();

            // Core transaction fields (Indonesian names as requested)
            $table->date('tanggal_transaksi');
            $table->string('kode_jasa'); // Auto unique and sequential per date (generated in model)

            // Relations to Karyawan (filtered by jabatan in the form); denormalized names for fast listing
            $table->foreignId('teknisi_karyawan_id')->nullable()->constrained('karyawan')->nullOnDelete();
            $table->foreignId('helper_karyawan_id')->nullable()->constrained('karyawan')->nullOnDelete();
            $table->string('teknisi_nama')->nullable();
            $table->string('helper_nama')->nullable();

            // Customer and service-related fields
            $table->string('nama_konsumen');
            $table->integer('garansi_hari')->nullable();

            // Aggregated totals maintained by model events
            $table->decimal('total_pendapatan_jasa', 15, 2)->default(0);
            $table->decimal('total_pengeluaran_jasa', 15, 2)->default(0);
            $table->decimal('total_keuntungan_jasa', 15, 2)->default(0);

            $table->text('keterangan')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Uniqueness: kode_jasa must be unique per date
            $table->unique(['tanggal_transaksi', 'kode_jasa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_jasas');
    }
};
