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
        Schema::create('unit_ac', function (Blueprint $table) {
            $table->id();

            $table->string('sku')->unique();
            $table->string('nama_unit');
            $table->foreignId('merk_id')->nullable()->constrained('merk')->onDelete('set null');
            $table->string('pk')->nullable();
            $table->foreignId('tipe_ac_id')->nullable()->constrained('tipe_ac')->onDelete('set null');
            $table->text('keterangan')->nullable();

            $table->json('path_foto_produk')->nullable();

            $table->integer('harga_dealer')->default(0);
            $table->integer('harga_ecommerce')->default(0);
            $table->integer('harga_retail')->default(0);

            $table->integer('stok_awal')->default(0);
            $table->integer('stok_akhir')->default(0);
            $table->integer('stok_masuk')->default(0);
            $table->integer('stok_keluar')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_ac');
    }
};
