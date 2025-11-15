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
        Schema::create('sparepart_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_sparepart_masuk')->unique();

            $table->date('tanggal_masuk');
            $table->foreignId('distributor_sparepart_id')->nullable()->constrained('distributor_spareparts')->onDelete('set null');
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
        Schema::dropIfExists('sparepart_masuk');
    }
};
