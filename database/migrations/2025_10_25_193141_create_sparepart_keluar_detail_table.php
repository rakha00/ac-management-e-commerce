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
        Schema::create('sparepart_keluar_detail', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sparepart_keluar_id')->constrained('sparepart_keluar')->onDelete('cascade');
            $table->foreignId('sparepart_id')->nullable()->constrained('spareparts')->onDelete('restrict');

            $table->integer('jumlah_keluar')->default(0);
            $table->integer('harga_modal')->default(0);
            $table->integer('harga_jual')->default(0);

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
        Schema::dropIfExists('sparepart_keluar_detail');
    }
};
