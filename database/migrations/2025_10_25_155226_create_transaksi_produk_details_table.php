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
        Schema::create('transaksi_produk_details', function (Blueprint $table) {
            $table->id();

            // Parent transaction relation
            $table->foreignId('transaksi_produk_id')
                ->constrained('transaksi_produks')
                ->onDelete('cascade'); // delete details when parent is deleted

            // Unit AC relation and denormalized product info
            $table->foreignId('unit_ac_id')
                ->nullable()
                ->constrained('unit_a_c_s')
                ->restrictOnDelete(); // keep detail if unit is removed, but restrict delete when referenced
            $table->string('sku');
            $table->string('nama_unit');

            // Reference pricing from UnitAC at time of transaction
            $table->decimal('harga_dealer', 15, 2);
            $table->decimal('harga_ecommerce', 15, 2);
            $table->decimal('harga_retail', 15, 2);

            // Transaction detail amounts
            $table->integer('jumlah_keluar');
            $table->decimal('harga_modal', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);

            // Notes
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
        Schema::dropIfExists('transaksi_produk_details');
    }
};
