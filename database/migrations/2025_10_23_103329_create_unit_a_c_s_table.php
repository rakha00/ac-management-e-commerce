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
        Schema::create('unit_a_c_s', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('nama_merk');
            $table->string('foto_produk')->nullable();
            $table->decimal('harga_dealer', 15, 2);
            $table->decimal('harga_ecommerce', 15, 2);
            $table->decimal('harga_retail', 15, 2);
            $table->integer('stock_awal')->default(0);
            $table->integer('stock_akhir')->default(0);
            $table->integer('stock_masuk')->default(0);
            $table->integer('stock_keluar')->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_a_c_s');
    }
};
