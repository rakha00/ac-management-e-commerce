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
        Schema::create('transaksi_jasa_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_jasa_id')->constrained('transaksi_jasa')->onDelete('cascade');

            $table->string('jenis_jasa');
            $table->integer('qty')->default(0);

            $table->integer('harga_jasa')->default(0);
            $table->text('keterangan_jasa')->nullable();

            $table->integer('pengeluaran_jasa')->default(0);
            $table->text('keterangan_pengeluaran')->nullable();

            $table->integer('subtotal_pendapatan')->default(0);
            $table->integer('subtotal_keuntungan')->default(0);

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
        Schema::dropIfExists('transaksi_jasa_detail');
    }
};
