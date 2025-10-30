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
        Schema::create('piutang_jasa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_jasa_id')->constrained('transaksi_jasa')->onDelete('cascade');
            $table->integer('total_piutang')->default(0);
            $table->string('status_pembayaran');
            $table->date('jatuh_tempo');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piutang_jasa');
    }
};
