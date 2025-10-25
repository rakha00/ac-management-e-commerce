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
        Schema::create('sparepart_keluars', function (Blueprint $table) {
            $table->id();

            // Core identity (Indonesian naming)
            $table->date('tanggal_keluar'); // transaction date
            $table->string('nomor_invoice'); // auto-generated per date, unique & sequential

            // Consumer info (sourced from konsumen_spareparts)
            $table->foreignId('konsumen_sparepart_id')
                ->nullable()
                ->constrained('konsumen_spareparts')
                ->nullOnDelete();
            $table->string('konsumen_nama')->nullable(); // denormalized consumer name

            // Aggregated totals
            $table->decimal('total_modal', 15, 2)->default(0);
            $table->decimal('total_penjualan', 15, 2)->default(0);
            $table->decimal('total_keuntungan', 15, 2)->default(0);

            // Notes
            $table->text('keterangan')->nullable();

            // Ensure nomor_invoice is unique per tanggal_keluar
            $table->unique(['tanggal_keluar', 'nomor_invoice'], 'unique_sparepart_keluar_invoice_per_tanggal');

            $table->timestamps();
            $table->softDeletes(); // enable soft deletes for historical accuracy
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sparepart_keluars');
    }
};
