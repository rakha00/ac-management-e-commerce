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
        Schema::create('spareparts', function (Blueprint $table) {
            $table->id();

            $table->json('path_foto_sparepart')->nullable();
            $table->string('kode_sparepart')->unique();
            $table->string('nama_sparepart');

            $table->integer('harga_modal')->default(0);
            $table->integer('harga_ecommerce')->default(0);

            $table->integer('stok_awal')->default(0);
            $table->integer('stok_akhir')->default(0);
            $table->integer('stok_masuk')->default(0);
            $table->integer('stok_keluar')->default(0);

            $table->text('keterangan')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spareparts');
    }
};
