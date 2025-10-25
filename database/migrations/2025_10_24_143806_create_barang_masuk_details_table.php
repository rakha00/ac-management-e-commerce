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
        Schema::create('barang_masuk_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_masuk_id')
                ->constrained('barang_masuks')
                ->cascadeOnDelete();
            $table->foreignId('unit_ac_id')
                ->constrained('unit_a_c_s')
                ->cascadeOnDelete();
            $table->string('sku')->nullable();
            $table->string('nama_unit')->nullable();
            $table->integer('jumlah_barang_masuk');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_masuk_details');
    }
};
