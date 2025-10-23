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
        Schema::create('petty_cashes', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->decimal('pemasukan', 15, 2)->nullable();
            $table->string('keterangan_pemasukan')->nullable();
            $table->decimal('pengeluaran', 15, 2)->nullable();
            $table->string('keterangan_pengeluaran')->nullable();
            $table->string('bukti_pembayaran')->nullable(); // Tambahan kolom foto
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cashes');
    }
};
