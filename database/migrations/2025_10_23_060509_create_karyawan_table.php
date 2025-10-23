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
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama');
            $table->string('jabatan');
            $table->string('no_hp');
            $table->integer('gaji_pokok');
            $table->text('alamat');
            $table->text('foto_ktp');
            $table->text('dokumen_tambahan');
            $table->string('kontak_darurat_serumah');
            $table->string('kontak_darurat_tidak_serumah');
            $table->boolean('status_aktif');
            $table->date('tanggal_terakhir_aktif')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
