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
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();

            // Relasi karyawan
            $table->foreignId('karyawan_id')
                ->constrained('karyawan')
                ->cascadeOnDelete();

            // Informasi waktu absen
            $table->date('tanggal');
            $table->dateTime('waktu_absen');

            // Status keterlambatan & keterangan
            $table->boolean('telat')->default(false);
            $table->string('keterangan')->nullable();

            // Konfirmasi admin
            $table->boolean('terkonfirmasi')->default(false);
            $table->foreignId('dikonfirmasi_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('waktu_konfirmasi')->nullable();

            $table->timestamps();

            // Unik absen per karyawan per hari
            $table->unique(['karyawan_id', 'tanggal']);
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
