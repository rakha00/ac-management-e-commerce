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
        Schema::create('transaksi_jasa', function (Blueprint $table) {
            $table->id();

            $table->date('tanggal_transaksi');
            $table->string('kode_jasa')->unique();

            $table->foreignId('teknisi_karyawan_id')->nullable()->constrained('karyawan')->onDelete('set null');
            $table->foreignId('helper_karyawan_id')->nullable()->constrained('karyawan')->onDelete('set null');
            $table->foreignId('konsumen_id')->nullable()->constrained('konsumen')->onDelete('set null');

            $table->integer('garansi_hari')->nullable()->default(0);

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
        Schema::dropIfExists('transaksi_jasa');
    }
};
