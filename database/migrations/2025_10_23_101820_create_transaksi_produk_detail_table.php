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
        Schema::create('transaksi_produk_detail', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaksi_produk_id')->constrained('transaksi_produk')->onDelete('cascade');

            $table->foreignId('unit_ac_id')->nullable()->constrained('unit_ac')->onDelete('restrict');
            $table->string('sku');
            $table->string('nama_unit');

            $table->integer('harga_dealer')->default(0);
            $table->integer('harga_ecommerce')->default(0);
            $table->integer('harga_retail')->default(0);

            $table->integer('jumlah_keluar')->default(0);
            $table->integer('harga_modal')->default(0);
            $table->integer('harga_jual')->default(0);

            $table->text('keterangan')->nullable();
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
        Schema::dropIfExists('transaksi_produk_detail');
    }
};
