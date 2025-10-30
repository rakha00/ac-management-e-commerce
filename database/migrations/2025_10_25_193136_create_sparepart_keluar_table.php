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
        Schema::create('sparepart_keluar', function (Blueprint $table) {
            $table->id();

            $table->date('tanggal_keluar');
            $table->string('nomor_invoice');

            $table->string('konsumen_nama')->nullable();

            $table->integer('total_modal')->default(0);
            $table->integer('total_penjualan')->default(0);
            $table->integer('total_keuntungan')->default(0);

            $table->text('keterangan')->nullable();

            $table->unique(['tanggal_keluar', 'nomor_invoice'], 'unique_sparepart_keluar_invoice_per_tanggal');

            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sparepart_keluar');
    }
};
