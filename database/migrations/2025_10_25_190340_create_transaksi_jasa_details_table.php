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
        Schema::create('transaksi_jasa_details', function (Blueprint $table) {
            $table->id();

            // Master relation: TransaksiJasa (cascade on delete)
            $table->foreignId('transaksi_jasa_id')
                ->constrained('transaksi_jasas')
                ->cascadeOnDelete();

            // Detail fields (Indonesian names as requested)
            $table->string('jenis_data'); // e.g., type of service or item
            $table->integer('qty'); // quantity of service/items

            // Pricing / cost fields
            $table->decimal('harga_jasa', 15, 2)->default(0); // revenue per item
            $table->text('keterangan_jasa')->nullable(); // optional description
            $table->decimal('pengeluaran_jasa', 15, 2)->default(0); // cost spent for the service
            $table->text('keterangan_pengeluaran')->nullable(); // optional notes for cost

            $table->timestamps();
            $table->softDeletes();

            // Optional indexing for faster lookups
            $table->index('transaksi_jasa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_jasa_details');
    }
};
