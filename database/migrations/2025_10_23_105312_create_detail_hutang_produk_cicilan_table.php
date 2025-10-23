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
        Schema::create('detail_hutang_produk_cicilan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hutang_produk_id')->constrained('hutang_produk')->onDelete('cascade');
            $table->integer('nominal_cicilan');
            $table->date('tanggal_cicilan');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_hutang_produk_cicilan');
    }
};
