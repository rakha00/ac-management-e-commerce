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
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->string('nama')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('nomor_hp')->nullable();
            $table->integer('gaji_pokok')->nullable()->default(0);
            $table->text('alamat')->nullable();

            $table->text('path_foto_ktp')->nullable();
            $table->text('path_dokumen_tambahan')->nullable();

            $table->string('kontak_darurat_serumah')->nullable();
            $table->string('kontak_darurat_tidak_serumah')->nullable();

            $table->boolean('status_aktif')->nullable()->default(true);
            $table->date('tanggal_terakhir_aktif')->nullable();

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
        Schema::dropIfExists('karyawan');
    }
};
