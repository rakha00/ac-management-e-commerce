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
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            // Relasi Karyawan
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            // Informasi waktu absen
            $table->date('tanggal');
            $table->dateTime('waktu_absen');
            // Status keterlambatan & keterangan
            $table->boolean('telat')->default(false);
            $table->string('keterangan')->nullable();
            // Konfirmasi admin
            $table->boolean('terkonfirmasi')->default(false);
            $table->foreignId('dikonfirmasi_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('waktu_konfirmasi')->nullable();
            // Unik absen per karyawan per hari
            $table->unique(['karyawan_id', 'tanggal']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
